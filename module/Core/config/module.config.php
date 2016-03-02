<?php

namespace Core;

return [
    'doctrine' => [
        'driver' => [
            'teaching_annotation_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Core/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Core\Entity' => 'teaching_annotation_driver',
                    'ZfcUser\Entity' => 'teaching_annotation_driver',
                ],
            ],
        ],
    ],
    'view_manager' => [
//        'template_map' => include __DIR__ . '/../template_map.php',
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
            ],
            [
                'type'        => 'phparray',
                'base_dir'    => __DIR__ . '/../language',
                'pattern'     => '/%s/Zend_Validate.php',
            ],
        ],
    ],
];