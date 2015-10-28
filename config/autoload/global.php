<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
            => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=zf2napratica;host=localhost',
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
            'dbname' => 'zf2napratica'
        )
    ),
    'acl' => array(
        'roles' => array(
            'visitante' => null,
            'redator' => 'visitante',
            'admin' => 'redator'
        ),
        'resources' => array(
            'Application\Controller\Index.index',
            'Application\Controller\Index.comments',
            'Admin\Controller\Index.save',
            'Admin\Controller\Index.delete',
            'Admin\Controller\Auth.index',
            'Admin\Controller\Auth.login',
            'Admin\Controller\Auth.logout',
            'Admin\Controller\User.index',
            'Admin\Controller\User.save',
            'Admin\Controller\User.delete',
        ),
        'privilege' => array(
            'visitante' => array(
                'allow' => array(
                    'Application\Controller\Index.index',
                    'Application\Controller\Index.comments',
                    'Admin\Controller\Auth.index',
                    'Admin\Controller\Auth.login',
                    'Admin\Controller\Auth.logout',
                )
            ),
            'redator' => array(
                'allow' => array(
                    'Admin\Controller\Index.save',
                )
            ),
            'admin' => array(
                'allow' => array(
                    'Admin\Controller\Index.delete',
                    'Admin\Controller\User.index',
                    'Admin\Controller\User.save',
                    'Admin\Controller\User.delete',
                )
            ),
        )
    )
);
