<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Authentication Guard
    |--------------------------------------------------------------------------
    |
    | This configuration option defines the authentication guard that will
    | be used to protect your admin routes. This option should match a
    | guard defined in your auth configuration file.
    |
    */
    'guard' => 'admin',

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    |
    | These options configure the routes for the admin panel.
    |
    */
    'routes' => [
        'prefix' => 'admin',
        'middleware' => ['web'],
        'name' => 'admin.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard
    |--------------------------------------------------------------------------
    |
    | These options configure the admin dashboard.
    |
    */
    'dashboard' => [
        'title' => 'Admin Dashboard',
        'widgets' => [
            'users_count' => true,
            'recent_users' => true,
            'system_info' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Menu
    |--------------------------------------------------------------------------
    |
    | These options configure the admin menu.
    |
    */
    'menu' => [
        'dashboard' => [
            'icon' => 'tachometer-alt',
            'title' => 'Dashboard',
            'route' => 'admin.dashboard',
            'permission' => 'admin.access',
        ],
        'users' => [
            'icon' => 'users',
            'title' => 'Users',
            'route' => 'admin.users.index',
            'permission' => 'user.view',
        ],
        'roles' => [
            'icon' => 'shield-alt',
            'title' => 'Roles & Permissions',
            'route' => 'admin.roles.index',
            'permission' => 'role.view',
        ],
        'config' => [
            'icon' => 'sliders-h',
            'title' => 'Configurations',
            'route' => 'admin.config.index',
            'permission' => 'admin.settings',
        ],
        'settings' => [
            'icon' => 'cogs',
            'title' => 'Settings',
            'route' => 'admin.settings.index',
            'permission' => 'admin.settings',
        ],
    ],
];
