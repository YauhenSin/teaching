<?php

return [
    'controllers' => [
        'invokables' => [
            'Admin\Controller\IndexController' => 'Admin\Controller\IndexController',
            'Admin\Controller\TeachersController' => 'Admin\Controller\TeachersController',
            'Admin\Controller\GroupsController' => 'Admin\Controller\GroupsController',
            'Admin\Controller\StudentsController' => 'Admin\Controller\StudentsController',
        ],
    ],
    'router' => [
        'routes' => [
            'admin_index_index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/admin/index[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'Admin\Controller\IndexController',
                        'action' => 'index',
                    ],
                ],
            ],
            'admin_teachers_index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/admin/teachers[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'Admin\Controller\TeachersController',
                        'action' => 'index',
                    ],
                ],
            ],
            'admin_groups_index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/admin/groups[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'Admin\Controller\GroupsController',
                        'action' => 'index',
                    ],
                ],
            ],
            'admin_students_index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/admin/students[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'Admin\Controller\StudentsController',
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
