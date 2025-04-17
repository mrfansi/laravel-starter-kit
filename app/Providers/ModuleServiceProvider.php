<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Platform\Core\Support\ModuleDiscovery;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerModules();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register all modules
     */
    protected function registerModules(): void
    {
        $modules = ModuleDiscovery::discover();

        foreach ($modules as $module => $provider) {
            $this->app->register($provider);
        }
    }
}
