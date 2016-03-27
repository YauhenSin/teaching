<?php

return [
    'controllers' => [
        'invokables' => [
            'Teacher\Controller\IndexController' => 'Teacher\Controller\IndexController',
            'Teacher\Controller\GroupsController' => 'Teacher\Controller\GroupsController',
            'Teacher\Controller\StudentsController' => 'Teacher\Controller\StudentsController',
        ],
    ],
    'router' => [
        'routes' => [
            'teacher_index_index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/teacher/index[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'Teacher\Controller\IndexController',
                        'action' => 'index',
                    ],
                ],
            ],
            'teacher_groups_index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/teacher/groups[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'Teacher\Controller\GroupsController',
                        'action' => 'index',
                    ],
                ],
            ],
            'teacher_students_index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/teacher/students[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'Teacher\Controller\StudentsController',
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
