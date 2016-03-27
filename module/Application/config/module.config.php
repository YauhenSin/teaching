<?php

namespace Application;

return [
    'controllers' => [
        'invokables' => [
            'Application\Controller\IndexController' => 'Application\Controller\IndexController',
        ],
    ],
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\IndexController',
                        'action'     => 'index',
                    ],
                ],
            ],
            'request' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/request',
                    'defaults' => [
                        'controller' => 'Application\Controller\IndexController',
                        'action'     => 'request',
                    ],
                ],
            ],
            'zfcuser/register' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/register',
                    'defaults' => [
                        'controller' => 'zfcuser',
                        'action' => 'register',
                    ],
                ],
            ],
            'zfcuser/login' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/login',
                    'defaults' => [
                        'controller' => 'zfcuser',
                        'action' => 'login',
                    ],
                ],
            ],
            'zfcuser/authenticate' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/authenticate',
                    'defaults' => [
                        'controller' => 'zfcuser',
                        'action' => 'authenticate',
                    ],
                ],
            ],
            'zfcuser/logout' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/logout',
                    'defaults' => [
                        'controller' => 'zfcuser',
                        'action' => 'logout',
                    ],
                ],
            ],
            'zfcuser/forgotpassword' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/forgot-password',
                    'defaults' => [
                        'controller' => 'goalioforgotpassword_forgot',
                        'action' => 'forgot',
                    ],
                ],
            ],
            'zfcuser/resetpassword' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/reset-password/:userId/:token',
                    'defaults' => [
                        'controller' => 'goalioforgotpassword_forgot',
                        'action' => 'reset',
                    ],
                    'constraints' => [
                        'userId'  => '[A-Fa-f0-9]+',
                        'token' => '[A-F0-9]+',
                    ],
                ],
            ],

            // disable unused routes
            'zfcuser' => [
                'options' => [
                    'route' => '',
                    'defaults' => [
                        'controller' => 'zfcuser',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'changepassword' => [
                        'options' => [
                            'defaults' => [
                                'controller' => null,
                            ],
                        ],
                    ],
                    'changeemail' => [
                        'options' => [
                            'defaults' => [
                                'controller' => null,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => include __DIR__ . '/../template_map.php',
    ],
];
