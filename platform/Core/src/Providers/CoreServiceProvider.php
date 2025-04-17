<?php

namespace Platform\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Platform\Core\Console\Commands\MakeModuleCommand;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register module services here
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeModuleCommand::class,
            ]);
        }
    }
}
