<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Register the Horizon service provider.
     */
    public function register(): void
    {
        // Only register Horizon if it's enabled in the environment
        if ($this->horizonEnabled()) {
            parent::register();
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Only boot Horizon if it's enabled in the environment
        if ($this->horizonEnabled()) {
            parent::boot();

            // Horizon::routeSmsNotificationsTo('15556667777');
            // Horizon::routeMailNotificationsTo('example@example.com');
            // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
        }
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user) {
            // Get allowed domains from config
            $allowedDomains = config('horizon.allowed_email_domains', []);
            
            if (empty($allowedDomains)) {
                return false;
            }
            
            // Extract domain from user email
            $emailParts = explode('@', $user->email);
            if (count($emailParts) !== 2) {
                return false;
            }
            
            $userDomain = $emailParts[1];
            
            // Check if user's email domain is in the allowed domains list
            return in_array($userDomain, $allowedDomains);
        });
    }

    /**
     * Determine if Horizon is enabled.
     *
     * @return bool
     */
    protected function horizonEnabled(): bool
    {
        return config('horizon.enabled', env('HORIZON_ENABLED', true));
    }
}
