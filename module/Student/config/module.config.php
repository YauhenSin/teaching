<?php

return [
    'controllers' => [
        'invokables' => [
            'Student\Controller\IndexController' => 'Student\Controller\IndexController',
        ],
    ],
    'router' => [
        'routes' => [
            'student_index_index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/student/index[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'Student\Controller\IndexController',
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
