<?php

namespace Platform\User\Providers;

use Platform\Core\Providers\ModuleServiceProvider;

class UserServiceProvider extends ModuleServiceProvider
{
    /**
     * Module name
     */
    protected string $moduleName = 'user';

    /**
     * Module path
     */
    protected string $modulePath = __DIR__ . '/../../';

    /**
     * Register services.
     */
    public function register(): void
    {
        parent::register();
        
        // Register module specific services here
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();
        
        // Bootstrap module specific services here
    }
}
