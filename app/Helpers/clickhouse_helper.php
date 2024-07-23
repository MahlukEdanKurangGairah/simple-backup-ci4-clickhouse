<?php
use mikehaertl\shellcommand\Command;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\StorageAttributes;

use CodeIgniter\CLI\CLI;

if(!function_exists('clickhouse_conf')){
    function clickhouse_conf(){
        helper('yaml_helper');
        return [
            'program' => APPPATH.'Executable/clickhouse',
            'parquet_dir' => FCPATH.'../writable/cache/parquet',
            'backup_dir' => FCPATH.'../writable/cache/backup',
            'MySqlConf' => readyaml('mysql_conf'),
        ];
    }
}

if(!function_exists('cl_create_parquet')){
    function cl_create_parquet($folder_parquet,$tbl){
        ini_set('memory_limit', -1);
        set_time_limit(0);
        helper([
            'myfile'
        ]);
        $arParam = clickhouse_conf();
        $arMysqlConf = $arParam['MySqlConf'];
        $mysqlConf = sprintf("'%s:%s','%s','%s','%s','%s'",
            $arMysqlConf['host'],
            $arMysqlConf['port'],
            $arMysqlConf['dbname'],
            $tbl,
            $arMysqlConf['username'],
            $arMysqlConf['password']
        );
        $strCommand = $arParam['program'].' -q "select * from mysql('.$mysqlConf.') format parquet;" > '.$folder_parquet.'/'.$tbl.'.parquet';
        CLI::write('Perintah Buat Parquet: ' . CLI::color($strCommand, 'green'));
        $cmd = new Command($strCommand);
        if($cmd->execute()){
            CLI::write('Done: '.CLI::color($cmd->getOutput(), 'green'));
        }else{
            CLI::write('Error: '.CLI::color($cmd->getOutput(), 'red'));
            CLI::write('Error: '.CLI::color($cmd->getExitCode(), 'red'));
        }
        chmod($folder_parquet.'/'.$tbl.'.parquet',0777);
    }
}

if(!function_exists('cl_create_csv')){
    function cl_create_csv($folder_parquet,$tbl){
        ini_set('memory_limit', -1);
        set_time_limit(0);
        helper([
            'myfile'
        ]);
        $arParam = clickhouse_conf();
        $arMysqlConf = $arParam['MySqlConf'];
        $mysqlConf = sprintf("'%s:%s','%s','%s','%s','%s'",
            $arMysqlConf['host'],
            $arMysqlConf['port'],
            $arMysqlConf['dbname'],
            $tbl,
            $arMysqlConf['username'],
            $arMysqlConf['password']
        );
        $strCommand = $arParam['program'].' -q "select * from mysql('.$mysqlConf.') format CsvWithNames;" > '.$folder_parquet.'/'.$tbl.'.csv';
        CLI::write('Perintah Buat Parquet: ' . CLI::color($strCommand, 'green'));
        $cmd = new Command($strCommand);
        if($cmd->execute()){
            CLI::write('Done: '.CLI::color($cmd->getOutput(), 'green'));
        }else{
            CLI::write('Error: '.CLI::color($cmd->getOutput(), 'red'));
            CLI::write('Error: '.CLI::color($cmd->getExitCode(), 'red'));
        }
        chmod($folder_parquet.'/'.$tbl.'.csv',0777);
    }
}

if(!function_exists('cl_create_sql_insert')){
    function cl_create_sql_insert($parquet_path,$sql_insert_path,$nama_table){
        ini_set('memory_limit', -1);
        set_time_limit(0);
        helper([
            'myfile'
        ]);
        $my_model = new \App\Models\MyModel();
        $arDescribe = $my_model->describeTable($nama_table);

        $arParam = clickhouse_conf();
        $arMysqlConf = $arParam['MySqlConf'];
        $mysqlConf = sprintf("'%s:%s','%s','%s','%s','%s'",
            $arMysqlConf['host'],
            $arMysqlConf['port'],
            $arMysqlConf['dbname'],
            $tbl,
            $arMysqlConf['username'],
            $arMysqlConf['password']
        );
        //$thead = ['MySQL', 'Parquet'];
        //$tbody = [];
        
        $my_model = new \App\Models\MyModel();
        $arDescribe = $my_model->describeTable($nama_table);
        $strDescribe = '';
        $fieldSelect = [];
        foreach($arDescribe as $vDescribe){
            if(strtolower($vDescribe['Type'])=='datetime' || strtolower($vDescribe['Type'])=='timestamp'){
                $fieldSelect[] = 'toDateTime('.$vDescribe['Field'].',\'Etc/UTC\') AS '.$vDescribe['Field'];
            }elseif(strtolower($vDescribe['Type'])=='date'){
                $fieldSelect[] = 'toDate('.$vDescribe['Field'].',\'Etc/UTC\') AS '.$vDescribe['Field'];
            }else{
                $fieldSelect[] = $vDescribe['Field'];
            }
        }
        //CLI::write('Done: '.CLI::color(json_encode($arDescribe), 'green'));

        /* $strCmdDescribe = $arParam['program'].' -q "DESCRIBE TABLE file(\''.$parquet_path.'\',parquet)"';
        $cmdDescribe = new Command($strCmdDescribe);
        if($cmdDescribe->execute()){
            CLI::write('Done: '.CLI::color($cmdDescribe->getOutput(), 'green'));
        }else{
            CLI::write('Error: '.CLI::color($cmdDescribe->getOutput(), 'red'));
            CLI::write('Error: '.CLI::color($cmdDescribe->getExitCode(), 'red'));
        } */
        //CLI::table($tbody, $thead);
        //return false;
        
        $cmdConvertSqlInsert = $arParam['program'].' -q "SELECT '.implode(', ',$fieldSelect).' FROM file(\''.$parquet_path.'\')'.
            ' FORMAT SQLInsert'.
            ' SETTINGS'.
            ' date_time_output_format=\'simple\','.
            ' output_format_sql_insert_max_batch_size=500,'.
            ' output_format_sql_insert_table_name=\''.$nama_table.'\';" > '.$sql_insert_path;
        CLI::write(CLI::color($cmdConvertSqlInsert, 'green'));
        $cmdConvert = new Command($cmdConvertSqlInsert);
        if($cmdConvert->execute()){
            chmod($sql_insert_path, 0777);
            CLI::write('Done: '.CLI::color($cmdConvert->getOutput(), 'green'));
        }else{
            CLI::write('Error: '.CLI::color($cmdConvert->getOutput(), 'red'));
            CLI::write('Error: '.CLI::color($cmdConvert->getExitCode(), 'red'));
        }
    }
}