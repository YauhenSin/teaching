<?php

namespace Core;

use Core\Controller\RedirectCallback;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\ControllerManager;
use Application\Controller\AuthController;

use Zend\Mvc\Application;
use Zend\Mvc\Router\RouteInterface;
use Zend\View\Helper\FlashMessenger;
use ZfcUser\Options\ModuleOptions;

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

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'teaching_redirect_callback' => function ($sm) {
                    /* @var RouteInterface $router */
                    $router = $sm->get('Router');

                    /* @var Application $application */
                    $application = $sm->get('Application');

                    /* @var ModuleOptions $options */
                    $options = $sm->get('zfcuser_module_options');

                    return new RedirectCallback($application, $router, $options);
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                'zfcuser' => function (ControllerManager $controllerManager) {
                    return new AuthController(
                        $controllerManager
                            ->getServiceLocator()
                            ->get('teaching_redirect_callback')
                    );
                }
            ]
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'flashMessenger' => function () {
                    $helper = new FlashMessenger();
                    $helper
                        ->setMessageOpenFormat(
                            '<div %s role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                        )
                        ->setMessageCloseString(
                            '</div>'
                        );
                    return $helper;
                }
            ],
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
