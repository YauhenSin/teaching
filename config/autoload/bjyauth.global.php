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
                ['route' => 'zfcuser/logout', 'roles' => ['guest', 'user', 'admin']],
                ['route' => 'zfcuser/forgotpassword', 'roles' => ['guest']],
                ['route' => 'zfcuser/resetpassword', 'roles' => ['guest']],
                ['route' => 'zfcuser_success_register', 'roles' => ['guest']],
            ],
        ],
    ],
];
