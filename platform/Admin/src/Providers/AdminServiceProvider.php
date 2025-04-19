<?php

namespace Platform\Admin\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Routing\Router;
use Platform\Admin\Http\Middleware\AdminAuthenticate;
use Platform\Admin\Http\Middleware\CheckAdminPermission;
use Platform\Admin\Http\Middleware\CheckAdminRole;
use Platform\Admin\Models\FactoryResolution;
use Platform\Core\Providers\ModuleServiceProvider;

class AdminServiceProvider extends ModuleServiceProvider
{
    /**
     * Module name
     */
    protected string $moduleName = 'admin';

    /**
     * Module path
     */
    protected string $modulePath = __DIR__.'/../../';

    /**
     * Register services.
     */
    public function register(): void
    {
        parent::register();

        // Register config
        $this->mergeConfigFrom(
            $this->modulePath.'config/admin.php', 'admin'
        );

        // Middleware registration moved to boot() method
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();

        // Register middleware
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('admin.auth', AdminAuthenticate::class);
        $router->aliasMiddleware('admin.role', CheckAdminRole::class);
        $router->aliasMiddleware('admin.permission', CheckAdminPermission::class);

        // Bootstrap factory resolution
        FactoryResolution::bootstrap();

        // Load migrations
        $this->loadMigrationsFrom("{$this->modulePath}database/migrations");

        // Load views
        $this->loadViewsFrom("{$this->modulePath}resources/views", 'admin');
        
        // Load Config module views
        $this->loadViewsFrom(base_path('platform/Config/resources/views'), 'config');

        // Load translations
        $this->loadTranslationsFrom($this->modulePath.'resources/lang', 'admin');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Register commands here
            ]);

            // Publish config
            $this->publishes([
                "{$this->modulePath}config/admin.php" => config_path('admin.php'),
            ], 'admin-config');

            // Publish views
            $this->publishes([
                "{$this->modulePath}resources/views" => resource_path('views/vendor/admin'),
            ], 'admin-views');

            // Publish assets
            $this->publishes([
                "{$this->modulePath}resources/assets" => public_path('vendor/admin'),
            ], 'admin-assets');
        }

        // Register policy
        $this->registerPolicies();
    }

    /**
     * Register policies.
     */
    protected function registerPolicies(): void
    {
        // Register policies here
    }
}
