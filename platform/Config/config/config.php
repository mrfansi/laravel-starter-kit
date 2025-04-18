<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configure how long configuration values should be cached.
    |
    */
    'cache' => [
        'enabled' => true,
        'expiration' => 86400, // 24 hours
        'prefix' => 'config_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Override Environment Variables
    |--------------------------------------------------------------------------
    |
    | When enabled, the configuration values from the database will override
    | environment variables and config files.
    |
    */
    'override_env' => true,

    /*
    |--------------------------------------------------------------------------
    | System Configuration Groups
    |--------------------------------------------------------------------------
    |
    | Define the configuration groups available in the system.
    |
    */
    'groups' => [
        'general' => 'General Settings',
        'mail' => 'Mail Settings',
        'database' => 'Database Settings',
        'app' => 'Application Settings',
        'auth' => 'Authentication Settings',
        'cache' => 'Cache Settings',
        'queue' => 'Queue Settings',
        'services' => 'External Services',
    ],
];
