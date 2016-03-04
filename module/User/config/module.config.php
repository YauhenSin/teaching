<?php

return [
    'controllers' => [
        'invokables' => [
            'User\Controller\ProfileController' => 'User\Controller\ProfileController',
            'User\Controller\SettingsController' => 'User\Controller\SettingsController',
            'User\Controller\ReviewsController' => 'User\Controller\ReviewsController',
            'User\Controller\DesignsController' => 'User\Controller\DesignsController',
            'User\Controller\WishlistController' => 'User\Controller\WishlistController',
            'User\Controller\PurchasesController' => 'User\Controller\PurchasesController',
            'User\Controller\SalesController' => 'User\Controller\SalesController',
            'User\Controller\PaymentsController' => 'User\Controller\PaymentsController',
            'User\Controller\CampaignsController' => 'User\Controller\CampaignsController',
        ],
    ],
    'router' => [
        'routes' => [
            'user_profile_index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/im[/:action]',
                    'defaults' => [
                        'controller' => 'User\Controller\ProfileController',
                        'action'     => 'index',
                    ],
                ],
            ],
            'user_account_settings' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/user/settings[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'User\Controller\SettingsController',
                        'action' => 'index',
                    ],
                ],
            ],
            'user_account_reviews' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/user/reviews[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'User\Controller\ReviewsController',
                        'action' => 'index',
                    ],
                ],
            ],
            'user_account_designs' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/user/designs[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'User\Controller\DesignsController',
                        'action' => 'index',
                    ],
                ],
            ],
            'user_account_wishlist' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/user/wishlist[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'User\Controller\WishlistController',
                        'action' => 'index',
                    ],
                ],
            ],
            'user_account_purchases' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/user/purchases[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'User\Controller\PurchasesController',
                        'action' => 'index',
                    ],
                ],
            ],
            'user_account_sales' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/user/sales[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'User\Controller\SalesController',
                        'action' => 'index',
                    ],
                ],
            ],
            'user_account_payments' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/user/payments[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'User\Controller\PaymentsController',
                        'action' => 'index',
                    ],
                ],
            ],
            'user_account_campaigns' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/user/campaigns[/:action[/:id]]',
                    'defaults' => [
                        'controller' => 'User\Controller\CampaignsController',
                        'action' => 'index',
                    ],
                ],
            ],
            'user_profile_twitter_complete' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/twitter-complete',
                    'defaults' => [
                        'controller' => 'User\Controller\ProfileController',
                        'action'     => 'twitter',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'menu' => [
        'user' => [
            'profile' => [
                'label' => 'My Account',
                'route' => 'user_profile_index',
                'pages' => [
                    'profile_designs' => [
                        'label' => 'My Designs',
                        'route' => 'user_account_designs',
                        'action' => 'index',
                        'pages' => [
                            'add_design' => [
                                'label' => 'Upload Design',
                                'route' => 'user_account_designs',
                                'action' => 'new',
                            ],
                            'edit_design' => [
                                'label' => 'Edit Design',
                                'route' => 'user_account_designs',
                                'action' => 'edit',
                            ],
                        ],
                    ],
                    'profile_payments' => [
                        'label' => 'Payments History',
                        'route' => 'user_account_payments',
                        'action' => 'index',
                    ],
                    'profile_purchases' => [
                        'label' => 'My Purchases',
                        'route' => 'user_account_purchases',
                        'action' => 'index',
                        'pages' => [
                            'view_purchase' => [
                                'label' => 'Order Details',
                                'route' => 'user_account_purchases',
                                'action' => 'view',
                            ],
                        ],
                    ],
//                    'profile_sales' => [
//                        'label' => 'My Sold Items',
//                        'route' => 'user_account_sales',
//                        'action' => 'index',
//                        'pages' => [
//                            'view_sale' => [
//                                'label' => 'Sold Item Details',
//                                'route' => 'user_account_sales',
//                                'action' => 'view',
//                            ],
//                        ],
//                    ],
                    'profile_campaigns' => [
                        'label' => 'My Campaigns',
                        'route' => 'user_account_campaigns',
                        'action' => 'index',
                    ],
                    'profile_wishlist' => [
                        'label' => 'Wishlist',
                        'route' => 'user_account_wishlist',
                        'action' => 'index',
                    ],
//                    'profile_reviews' => [
//                        'label' => 'Reviews',
//                        'route' => 'user_account_reviews',
//                        'action' => 'index',
//                        'pages' => [
//                            'edit_review' => [
//                                'label' => 'Edit Review',
//                                'route' => 'user_account_reviews',
//                                'action' => 'edit',
//                            ],
//                        ],
//                    ],
//                    'profile_invite' => [
//                        'label' => 'Invite & earn',
//                        'route' => 'user_profile_index',
//                        'action' => 'invite',
//                    ],
                    'profile_settings' => [
                        'label' => 'Settings',
                        'route' => 'user_account_settings',
                        'action' => 'edit-profile',
                        'pages' => [
                            'add_address' => [
                                'label' => 'Add Address',
                                'route' => 'user_account_settings',
                                'action' => 'add-address',
                            ],
                            'edit_address' => [
                                'label' => 'Edit Address',
                                'route' => 'user_account_settings',
                                'action' => 'edit-address',
                            ],
                            'reset_password' => [
                                'label' => 'Reset Password',
                                'route' => 'user_account_settings',
                                'action' => 'reset-password',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
