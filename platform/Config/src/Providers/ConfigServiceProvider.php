<?php

namespace Platform\Config\Providers;

use Illuminate\Support\ServiceProvider;
use Platform\Config\Console\Commands\InstallConfigModuleCommand;
use Platform\Config\Console\Commands\SyncConfigCommand;
use Platform\Config\Http\Middleware\LoadDatabaseConfigurations;
use Platform\Config\Services\ConfigService;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('platform.config', function ($app) {
            return new ConfigService();
        });

        // Register the config repository to override Laravel's config
        $this->app->singleton('platform.config.repository', function ($app) {
            return new ConfigRepository($app->make('platform.config'));
        });

        // Register the middleware singleton
        $this->app->singleton('platform.config.middleware', function ($app) {
            return new LoadDatabaseConfigurations();
        });

        // Register the middleware in the HTTP kernel
        $this->app->booted(function () {
            $kernel = $this->app->make('Illuminate\Foundation\Http\Kernel');
            $kernel->pushMiddleware(LoadDatabaseConfigurations::class);
        });

        // Merge module configuration
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'platform.config');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register middleware globally
        $this->app->singleton('platform.config.middleware', function ($app) {
            return new LoadDatabaseConfigurations();
        });
        
        // We'll register the middleware in the app service provider
        // This ensures our configurations are loaded early in the request lifecycle
        
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'config');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncConfigCommand::class,
                InstallConfigModuleCommand::class,
            ]);
        }
    }
}
