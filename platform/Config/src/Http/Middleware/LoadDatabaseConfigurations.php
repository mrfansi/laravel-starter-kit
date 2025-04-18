<?php

namespace Platform\Config\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Platform\Config\Facades\Config as DynamicConfig;
use Symfony\Component\HttpFoundation\Response;

class LoadDatabaseConfigurations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only load configurations if enabled in config
        if (config('platform.config.override_env', true)) {
            // Get all configurations from the database
            $configurations = DynamicConfig::getAllByGroup();
            
            // Override Laravel's config with database values
            foreach ($configurations as $key => $value) {
                config([$key => $value]);
            }
        }
        
        return $next($request);
    }
}
