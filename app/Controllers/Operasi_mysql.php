<?php
namespace App\Controllers;
use CodeIgniter\CLI\CLI;
class Operasi_mysql extends \CodeIgniter\Controller{
    private $backup_dir;
    public function __construct(){
        helper([
            'yaml','clickhouse','mysqlclient','myfile','python'
        ]);
        $this->my_model = new \App\Models\MyModel();
        $this->backup_dir = '/var/www/html/backup_folder';
    }
    public function run(){
        CLI::clearScreen();
        $pilihan = CLI::promptByKey('Masukkan angka pilihan anda dan tekan enter (default: x)', [
            'x'=>'Exit',
            'b'=>'Backup Database',
            'r'=>'Restore Database',
        ]);
        
        if(strtolower(trim($pilihan))=='x'){
            CLI::write('Exit');
            exit;
        }elseif(strtolower(trim($pilihan))=='b'){
            $nama_folder = CLI::prompt('Masukan nama folder tempat backup data: ',date('Ymd'));
            $this->create_backup_mysql_parquet($nama_folder);
        }elseif(strtolower(trim($pilihan))=='r'){
            $nama_folder = CLI::prompt('Masukan nama folder tempat backup data: ',date('Ymd'));
            $this->restore_db($nama_folder);
        }
    }
    public function restore_mysql($folder){
        restore_mysql('20240715');
    }
    public function restore_mysql_parquet($folder){
        //restore_mysql('20240715');
    }
    public function create_backup_mysql($folder){
        //restore_mysql('20240715');
    }
    public function create_backup_mysql_parquet($folder){
        CLI::write('Mulai Buat Backup: '.date('Y-m-d H:i:s'));
        $arTable = $this->my_model->showTables();
        $totalTable = count($arTable);
        if($totalTable<1){
            CLI::write('Table Tidak Ditemukan');
            return PHP_EOL;
        }
        $thead = ['No.', 'Table'];
        $tbody = [];
        $no_urut = 1;
        foreach ($arTable as $table){
            $tbody[] = [
                $no_urut,
                $table[key($table)],
            ];
            $no_urut++;
        }
        CLI::table($tbody, $thead);
        
        $base_dir = $this->backup_dir;
        $operation_dir = $base_dir.'/'.$folder;
        $struktur_dir = $operation_dir.'/struktur';
        $parquet_dir = $operation_dir.'/parquet';
        $insert_data_dir = $operation_dir.'/insert_data_sql';
        //if(!folder_exist($this->backup_dir,$folder)){
            create_folder($this->backup_dir,$folder);
            chmod($operation_dir, 0777);
        //}
        //if(!folder_exist($operation_dir,'struktur')){
            create_folder($operation_dir,'struktur');
            chmod($struktur_dir, 0777);
        //}
        //if(!folder_exist($operation_dir,'parquet')){
            create_folder($operation_dir,'parquet');
            chmod($parquet_dir, 0777);
        //}
        //if(!folder_exist($operation_dir,'parquet')){
            create_folder($operation_dir,'insert_data_sql');
            chmod($insert_data_dir, 0777);
        //}
        $no_tugas = 1;
        $totalTugas = (2*$totalTable);
        CLI::write('Create File Struktur .sql');
        foreach ($arTable as $table) {
            $table_name = $table[key($table)];
            $arStruktur = $this->my_model->showCreateTables($table_name);
            $isi = $arStruktur[0]['Create Table'];
            $isi = str_replace('CREATE TABLE','CREATE TABLE IF NOT EXISTS',$isi);
            //$isi = str_replace(' datetime ',' timestamp ',$isi);
            $isi = preg_replace('/ENGINE.*/i',';',$isi);
            
            create_file($struktur_dir,$table_name.'.sql',$isi);
            $path_sql = $struktur_dir.'/'.$table_name.'.sql';
            chmod($path_sql, 0777);
            
            CLI::showProgress($no_tugas, $totalTugas);
            CLI::newLine();
            CLI::write($table_name.'.sql Selesai');
            $no_tugas++;
        }
        CLI::write('Create Data Parquet.');
        foreach ($arTable as $table) {
            $table_name = $table[key($table)];
            cl_create_parquet($parquet_dir,$table_name);
            
            CLI::showProgress($no_tugas, $totalTugas);
            CLI::newLine();
            CLI::write($table_name.'.parquet Selesai');
            $no_tugas++;
        }
        CLI::showProgress(false);
        CLI::newLine();
        CLI::write('Selesai: ' . CLI::color(date('Y-m-d H:i:s'), 'red'));
        exit();
    }
    public function restore_db($backupFolder){
        $arFolder = folder_exist($backupFolder);
        $jmlFolder = count($arFolder);
        CLI::write('Mulai Restore: '.CLI::color(date('Y-m-d H:i:s'), 'green'));
        
        if($jmlFolder!=3){
            CLI::write('Keluar. ' . CLI::color('Jml Folder Aneh -'.$jmlFolder.'-', 'red'));
            exit;
        }else{
            for($i=0;$i<$jmlFolder;$i++){
                $namaFolder = str_replace($backupFolder.'/','',$arFolder[$i]);
                CLI::write('Folder: ' . CLI::color($namaFolder,'red'));
            }
            $base_folder = '/var/www/html/backup_folder/';
            $backup_folder = $base_folder.$backupFolder;
            
            $arFileSql = list_file($backup_folder,'struktur');
            $jmlFileSql = count($arFileSql);

            $arFileParquet = list_file($backup_folder,'parquet');
            $jmlFileParquet = count($arFileParquet);
            $totalTugas = $jmlFileSql+$jmlFileParquet;
            
            $no_tugas = 1;
            for($i=0;$i<$jmlFileSql;$i++){
                $no_tugas++;
                $sql_path_create_table = $backup_folder.'/'.$arFileSql[$i];
                CLI::showProgress($no_tugas, $totalTugas);
                //restore_mysql($file_path);
                py_sql2mysql($sql_path_create_table);
            }
            for($x=0;$x<$jmlFileParquet;$x++){
                $no_tugas++;
                $parquet_path = $backup_folder.'/'.$arFileParquet[$x];
                $tbl_name = str_replace('parquet/','',$arFileParquet[$x]);
                $tbl_name = str_replace('.parquet','',$tbl_name);
                $sql_insert_path = $backup_folder.'/insert_data_sql/'.$tbl_name.'.sql';
                CLI::showProgress($no_tugas, $totalTugas);
                cl_create_sql_insert($parquet_path,$sql_insert_path,$tbl_name);
                //restore_mysql($sql_insert_path);
                py_sql2mysql($sql_insert_path);
            }
        }
        CLI::showProgress(false);
        CLI::newLine();
        CLI::write('Selesai: ' . CLI::color(date('Y-m-d H:i:s'), 'red'));
        exit;
    }
}