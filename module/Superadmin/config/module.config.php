<?php

return [
    'controllers' => [
        'invokables' => [
            'Superadmin\Controller\IndexController' => 'Superadmin\Controller\IndexController',
            'Superadmin\Controller\AdminsController' => 'Superadmin\Controller\AdminsController',
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
        ],
    ],
    'view_manager' => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
];
