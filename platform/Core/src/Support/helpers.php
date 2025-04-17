<?php

if (!function_exists('module_path')) {
    /**
     * Get the path to a module
     *
     * @param string $name
     * @param string $path
     * @return string
     */
    function module_path(string $name, string $path = ''): string
    {
        $modulePath = base_path('platform/' . $name);
        
        return $modulePath . ($path ? '/' . $path : '');
    }
}

if (!function_exists('module_asset')) {
    /**
     * Get the URL to a module asset
     *
     * @param string $name
     * @param string $path
     * @return string
     */
    function module_asset(string $name, string $path): string
    {
        return asset('modules/' . $name . '/' . $path);
    }
}
