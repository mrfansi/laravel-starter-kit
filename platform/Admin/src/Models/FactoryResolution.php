<?php

namespace Platform\Admin\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Admin\Database\Factories\AdminFactory;
use Platform\Admin\Database\Factories\RoleFactory;
use Platform\Admin\Database\Factories\PermissionFactory;
use Platform\Admin\Database\Factories\ConfigurationFactory;

/**
 * This class is responsible for resolving the factory classes for the Admin platform models.
 */
class FactoryResolution
{
    /**
     * Bootstrap the factory resolution for Admin platform models.
     *
     * @return void
     */
    public static function bootstrap(): void
    {
        // Register the model factories
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            // Handle both namespaced and non-namespaced model names
            $className = class_basename($modelName);
            
            return match ($className) {
                'Admin' => AdminFactory::class,
                'Role' => RoleFactory::class,
                'Permission' => PermissionFactory::class,
                'Configuration' => ConfigurationFactory::class,
                default => null,
            };
        });
    }
}
