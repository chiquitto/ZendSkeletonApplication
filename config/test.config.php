<?php

return array(
    'db' => array(
        'driver' => 'PDO',
        'dsn' => 'mysql:dbname=zf2napratica_test;host=localhost',
        'username' => 'root',
        'password' => '123456',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'doctrine' => array(
        'connection' => array(
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'root',
            'password' => '123456',
            'dbname' => 'zf2napratica_test'
        )
    ),
);
