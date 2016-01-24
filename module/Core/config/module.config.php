<?php

return array(
    'di' => array(),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => function ($sm) {
                $config = $sm->get('Configuration');
                $dbParams = $config['db'];

                $adapter = new \BjyProfiler\Db\Adapter\ProfilingAdapter(array(
                    'driver' => 'pdo',
                    'dsn' => $dbParams['dsn'],
                    'database' => 'zf2napratica',
                    'username' => $dbParams['username'],
                    'password' => $dbParams['password'],
                    'hostname' => 'localhost',
                ));

                if (php_sapi_name() == 'cli') {
                    $logger = new \Zend\Log\Logger();
                    // write queries profiling info to stdout in CLI mode
                    $writer = new \Zend\Log\Writer\Stream('php://output');
                    $logger->addWriter($writer, \Zend\Log\Logger::DEBUG);
                    $adapter->setProfiler(new \BjyProfiler\Db\Profiler\LoggingProfiler($logger));
                } else {
                    $adapter->setProfiler(new \BjyProfiler\Db\Profiler\Profiler());
                }
                if (isset($dbParams['options']) && is_array($dbParams['options'])) {
                    $options = $dbParams['options'];
                } else {
                    $options = array();
                }
                $adapter->injectProfilingStatementPrototype($options);

                return $adapter;
            },
                ),
            ),
            'view_helpers' => array(
                'invokables' => array(
                    'session' => 'Core\View\Helper\Session'
                ),
            ),
        );
        