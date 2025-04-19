<?php

namespace Platform\Core\Providers;

use Illuminate\Support\ServiceProvider;

abstract class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Module name
     */
    protected string $moduleName = '';

    /**
     * Module path
     */
    protected string $modulePath = '';

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
        $this->registerViews();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom("{$this->modulePath}/database/migrations");
        $this->loadViewsFrom("{$this->modulePath}/resources/views", $this->moduleName);
        $this->loadRoutesFrom("{$this->modulePath}/routes/web.php");
        
        // Only load API routes if the file exists
        $apiRoutesPath = "{$this->modulePath}/routes/api.php";
        if (file_exists($apiRoutesPath)) {
            $this->loadRoutesFrom($apiRoutesPath);
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = "{$this->modulePath}/config/{$this->moduleName}.php";

        if (file_exists($configPath)) {
            $this->publishes([
                $configPath => config_path("{$this->moduleName}.php"),
            ], 'config');

            $this->mergeConfigFrom(
                $configPath, $this->moduleName
            );
        }
    }

    /**
     * Register views.
     */
    protected function registerViews(): void
    {
        $viewsPath = "{$this->modulePath}/resources/views";

        $this->publishes([
            $viewsPath => resource_path("views/modules/{$this->moduleName}"),
        ], 'views');
    }
}
