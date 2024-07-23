<?php
use CodeIgniter\Router\RouteCollection;
$routes->get('/', 'Home::index');
$routes->cli('/', 'Operasi_mysql::run');
