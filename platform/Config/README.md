# Config Module

## Overview

The Config module provides a dynamic configuration system for the Laravel Starter Kit. It allows you to store and manage configuration values in a database, which can be modified at runtime without requiring application restarts or code deployments.

## Features

- Store configuration values in the database
- Override Laravel's built-in configuration system
- Admin interface for managing configurations
- API endpoints for accessing public configurations
- Import/export functionality for easy migration between environments
- Type casting for different data types (string, boolean, integer, array, JSON)
- Group configurations for better organization
- Cache configurations for better performance

## Installation

The Config module is included in the Laravel Starter Kit by default. If you need to install it manually, follow these steps:

1. Make sure the module is registered in your `composer.json` file
2. Run the migrations: `php artisan migrate`
3. Seed the initial configurations: `php artisan db:seed --class="Platform\Config\Database\Seeders\ConfigurationSeeder"`

## Usage

### Using the Facade

You can use the `DynamicConfig` facade to access and modify configuration values:

```php
use Platform\Config\Facades\Config as DynamicConfig;

// Get a configuration value
$appName = DynamicConfig::get('app.name');

// Set a configuration value
DynamicConfig::set('app.name', 'My Application');

// Check if a configuration exists
if (DynamicConfig::has('app.debug')) {
    // Do something
}

// Delete a configuration
DynamicConfig::delete('app.debug');

// Get all configurations by group
$mailConfigs = DynamicConfig::getAllByGroup('mail');
```

### Using the Service

You can also inject the `ConfigService` into your classes:

```php
use Platform\Config\Services\ConfigService;

class MyService
{
    protected $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function doSomething()
    {
        $appName = $this->configService->get('app.name');
        // ...
    }
}
```

### Admin Interface

The Config module provides an admin interface for managing configurations. You can access it at `/admin/config`.

### API Endpoints

The Config module provides the following API endpoints:

- `GET /api/config` - Get all public configurations
- `GET /api/config/{key}` - Get a specific public configuration
- `PUT /api/config/{key}` - Update a configuration (requires authentication)
- `POST /api/config/batch` - Batch update configurations (requires authentication)

## Configuration

The Config module itself can be configured in `config/platform.config.php`. The following options are available:

- `cache.enabled` - Whether to cache configurations
- `cache.expiration` - How long to cache configurations (in seconds)
- `cache.prefix` - Prefix for cache keys
- `override_env` - Whether to override environment variables with database configurations
- `groups` - Available configuration groups

## Extending

You can extend the Config module by adding custom configuration groups or types. Simply update the `config/platform.config.php` file with your custom groups.

## License

The Config module is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
