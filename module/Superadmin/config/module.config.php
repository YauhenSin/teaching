<?php

return [
    'controllers' => [
        'invokables' => [
            'Superadmin\Controller\IndexController' => 'Superadmin\Controller\IndexController',
            'Superadmin\Controller\AdminsController' => 'Superadmin\Controller\AdminsController',
            'Superadmin\Controller\RequestsController' => 'Superadmin\Controller\RequestsController',
        ],
    ],
    'router' => [
        'routes' => [
            'superadmin_index_index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/superadmin/index[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'Superadmin\Controller\IndexController',
                        'action' => 'index',
                    ],
                ],
            ],
            'superadmin_admins_index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/superadmin/admins[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'Superadmin\Controller\AdminsController',
                        'action' => 'index',
                    ],
                ],
            ],
            'superadmin_requests_index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/superadmin/requests[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'Superadmin\Controller\RequestsController',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
];
