<?php

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\ControllerManager;
use Application\Controller\AuthController;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                'zfcuser' => function (ControllerManager $controllerManager) {
                    return new AuthController(
                        $controllerManager
                            ->getServiceLocator()
                            ->get('zfcuser_redirect_callback')
                    );
                }
            ]
        ];
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
