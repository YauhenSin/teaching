<?php

return [
    'controllers' => [
        'invokables' => [
            'Superadmin\Controller\IndexController' => 'Superadmin\Controller\IndexController',
            'Superadmin\Controller\AdminController' => 'Superadmin\Controller\AdminController',
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
            'superadmin_admin_index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/superadmin/admin[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'Superadmin\Controller\AdminController',
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
