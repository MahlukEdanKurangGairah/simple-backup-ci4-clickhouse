<?php
use mikehaertl\shellcommand\Command;
use CodeIgniter\CLI\CLI;

if(!function_exists('restore_parquet2mysql')){
    function restore_parquet2mysql($path_parquet){
        if(!preg_match('/parquet$/',$path_parquet)) return;
        helper('yaml_helper');
        $parquet_dir = '/var/www/html/backup_folder/'.$backup_dir;
        $arMySqlConf = readyaml('mysql_conf');
        $program = APPPATH.'Executable/parquet2mysql/parquet2mysql.py';
        $yaml_file_path = APPPATH.'Config/App.yaml';
        $strCommand = sprintf('/usr/bin/python3 %s %s %s',
            $program,
            $path_parquet,
            $yaml_file_path
        );
        CLI::write($strCommand);
        $cmd = new Command($strCommand);
        if($cmd->execute()){
            CLI::write('Done: ' . CLI::color($cmd->getOutput(), 'green'));
        }else{
            CLI::write('Error: ' . CLI::color($cmd->getOutput(), 'red'));
            CLI::write('Error: ' . CLI::color($cmd->getExitCode(), 'red'));
        }
    }
}

if(!function_exists('py_sql2mysql')){
    function py_sql2mysql($sql_path){
        //if(!preg_match('/insert_data_sql/',$sql_path)) return;
        $program = APPPATH.'Executable/sql2mysql/sql2mysql.py';
        $yaml_file_path = APPPATH.'Config/App.yaml';
        $strCommand = sprintf('/usr/bin/python3 %s %s %s',
            $program,
            $yaml_file_path,
            $sql_path
        );
        CLI::write(CLI::color($strCommand, 'yellow'));
        $cmd = new Command($strCommand);
        if($cmd->execute()){
            CLI::write('Done: ' . CLI::color($cmd->getOutput(), 'green'));
        }else{
            CLI::write('Error: ' . CLI::color($cmd->getOutput(), 'red'));
            CLI::write('Error: ' . CLI::color($cmd->getExitCode(), 'red'));
        }
    }
}