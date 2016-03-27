<?php

return [
    'bjyauthorize' => [
        'default_role' => 'guest',
        'identity_provider' => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',

        'role_providers' => [
            'BjyAuthorize\Provider\Role\Config' => [
                'guest' => [],
                'student' => [],
                'teacher' => [],
                'admin' => [],
                'superadmin' => [],
            ],
            'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => [
                'object_manager'    => 'doctrine.entitymanager.orm_default',
                'role_entity_class' => 'Core\Entity\Role',
            ],
        ],
        'guards' => [
            'BjyAuthorize\Guard\Route' => [
                ['route' => 'zfcuser', 'roles' => ['guest']],
                ['route' => 'zfcuser/register', 'roles' => ['guest']],
                ['route' => 'zfcuser/login', 'roles' => ['guest']],
                ['route' => 'zfcuser/authenticate', 'roles' => ['guest']],
                ['route' => 'zfcuser/logout', 'roles' => ['student', 'teacher', 'admin', 'superadmin']],
                ['route' => 'zfcuser/forgotpassword', 'roles' => ['guest']],
                ['route' => 'zfcuser/resetpassword', 'roles' => ['guest']],

                ['route' => 'home', 'roles' => ['guest', 'student', 'teacher', 'admin', 'superadmin']],
                ['route' => 'request', 'roles' => ['guest', 'student', 'teacher', 'admin', 'superadmin']],

                ['route' => 'superadmin_index_index', 'roles' => ['superadmin']],
                ['route' => 'superadmin_admins_index', 'roles' => ['superadmin']],
                ['route' => 'superadmin_requests_index', 'roles' => ['superadmin']],

                ['route' => 'admin_index_index', 'roles' => ['admin']],
                ['route' => 'admin_teachers_index', 'roles' => ['admin']],
                ['route' => 'admin_groups_index', 'roles' => ['admin']],
                ['route' => 'admin_students_index', 'roles' => ['admin']],

                ['route' => 'teacher_index_index', 'roles' => ['teacher']],
                ['route' => 'teacher_groups_index', 'roles' => ['teacher']],
                ['route' => 'teacher_students_index', 'roles' => ['teacher']],

                ['route' => 'student_index_index', 'roles' => ['student']],
            ],
        ],
    ],
];
