<?php

namespace Core\Db;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\Adapter;

/**
 * Factory to build a DbAdapter
 *
 * @category   Core
 * @package    Db
 */
class AdapterServiceFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        $config = $serviceLocator->get('Configuration');
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
    }

    public function createService1(ServiceLocatorInterface $serviceLocator) {
        $config = $serviceLocator->get('Configuration');
        $mvcEvent = $serviceLocator->get('Application')->getMvcEvent();
        if ($mvcEvent) {
            $routeMatch = $mvcEvent->getRouteMatch();
            $moduleName = $routeMatch->getParam('module');
            //if the module have a db configuration use it
            $moduleConfig = include getenv('PROJECT_ROOT') . '/module/' . ucfirst($moduleName) . '/config/module.config.php';
            if (isset($moduleConfig['db']))
                $config['db'] = $moduleConfig['db'];
        }
        return new Adapter($config['db']);
    }

}
