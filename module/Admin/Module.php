<?php

namespace Admin;

use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;

class Module {

    public function onBootstrap($event) {
        /* @var $app Application */
        $app = $event->getApplication();

        $moduleManager = $app
                ->getServiceManager()
                ->get('modulemanager')
        ;

        $sharedEvents = $moduleManager->getEventManager()
                ->getSharedManager();

        $sharedEvents->attach(
                'Zend\Mvc\Controller\AbstractActionController', MvcEvent::EVENT_DISPATCH, array($this, 'mvcPreDispatch'), 100
        );
    }

    /**
     * Verifica se precisa fazer a autorização do acesso
     * @param  MvcEvent $event Evento
     * @return boolean
     */
    public function mvcPreDispatch($event) {
        $routeMatch = $event->getRouteMatch();
        $moduleName = $routeMatch->getParam('module');
        $controllerName = $routeMatch->getParam('controller');
        $actionName = $routeMatch->getParam('action');

        $di = $event->getTarget()->getServiceLocator();
        $authService = $di->get('Admin\Service\Auth');

        if (!$authService->authorize($moduleName, $controllerName, $actionName)) {
            throw new \Exception('Você não tem permissão para acessar este recurso');
        }
        return true;
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

}
