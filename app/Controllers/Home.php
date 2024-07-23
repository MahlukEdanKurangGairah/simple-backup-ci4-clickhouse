<?php
namespace App\Controllers;
class Home extends BaseController{
    private $backup_dir;
    public function __construct(){
        helper([
            'yaml','clickhouse','mysqlclient'
        ]);
        $this->my_model = new \App\Models\MyModel();
    }
    public function index(){
        /* $arTable = $this->my_model->showTables();
        foreach ($arTable as $table) {
            $table_name = $table[key($table)];
            create_parquet($table_name);
        }
        restore_mysql('20240715');
        return view('welcome_message',[
            'ar_file'=>'',
        ]); */
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
        }
    }
    public function backup_list(){}
    public function create_backup(){
        $folder = date('Ymd_Hi');
    }
}