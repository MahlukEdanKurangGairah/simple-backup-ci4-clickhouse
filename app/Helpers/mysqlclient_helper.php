<?php
use mikehaertl\shellcommand\Command;
use CodeIgniter\CLI\CLI;

if(!file_exists('restore_mysql')){
    function restore_mysql($sql_file){
        if(!preg_match('/sql$/',$sql_file)) return;
        helper('yaml_helper');
        $arMysqlConf = readyaml('mysql_restore_conf');
        $mycnf = APPPATH.'Config/client_restore.cnf';
        $strCommand = sprintf('/usr/bin/mysql --defaults-file=%s --host=%s --port=%s --database=%s < '.$sql_file,
            $mycnf,    
            $arMysqlConf['host'],
            $arMysqlConf['port'],
            $arMysqlConf['dbname']
        );
        CLI::write(CLI::color($strCommand, 'green'));
        $cmd = new Command($strCommand);
        if($cmd->execute()){
            CLI::write('Done: '.CLI::color($cmd->getOutput(), 'green'));
        }else{
            CLI::write('Error: '.CLI::color($cmd->getOutput(), 'red'));
            CLI::write('Error: '.CLI::color($cmd->getExitCode(), 'red'));
        }
    }
}