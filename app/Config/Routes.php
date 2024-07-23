<?php
use CodeIgniter\Router\RouteCollection;
$routes->get('/', 'Home::index');
$routes->cli('/', 'Operasi_mysql::run');
$routes->group('operasi-mysql',static function($routes){
    $routes->cli('/', 'Operasi_mysql::run');
    $routes->cli('backup-mysql-parquet/(:any)', 'Operasi_mysql::create_backup_mysql_parquet/$1');
});