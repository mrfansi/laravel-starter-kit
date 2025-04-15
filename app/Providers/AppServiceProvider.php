<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
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
        Gate::define('viewPulse', function ($user) {
            $allowedDomains = config('pulse.allowed_domains', []);

            $allowedDomains = array_map('trim', $allowedDomains);

            $allowedDomains = array_filter($allowedDomains);

            if (empty($allowedDomains)) {
                return false;
            }

            return Str::endsWith($user->email, $allowedDomains);
        });
    }
}
