<?php

namespace Platform\Config\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;

class ConfigAppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the middleware globally
        $kernel = $this->app->make(Kernel::class);
        if (method_exists($kernel, 'prependMiddleware')) {
            $kernel->prependMiddleware($this->app->make('platform.config.middleware'));
        } elseif (method_exists($kernel, 'pushMiddleware')) {
            $kernel->pushMiddleware($this->app->make('platform.config.middleware'));
        } else {
            // For Laravel 8+, we can use the middleware priority
            app('router')->middlewarePriority = array_merge(
                [get_class($this->app->make('platform.config.middleware'))],
                app('router')->middlewarePriority
            );
            app('router')->middleware(['platform.config' => get_class($this->app->make('platform.config.middleware'))]);
        }
    }
}
