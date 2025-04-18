<?php

namespace Platform\Core\Support;

class ModuleManager
{
    /**
     * Get all available modules
     */
    public static function getModules(): array
    {
        $modules = [];
        $modulesPath = base_path('platform');
        $directories = array_filter(glob("$modulesPath/*"), 'is_dir');

        foreach ($directories as $directory) {
            $moduleName = basename($directory);
            if ($moduleName !== 'Core' && file_exists("$directory/composer.json")) {
                $modules[] = $moduleName;
            }
        }

        return $modules;
    }

    /**
     * Check if a module exists
     */
    public static function hasModule(string $name): bool
    {
        return in_array($name, self::getModules());
    }

    /**
     * Get module path
     */
    public static function getModulePath(string $name): ?string
    {
        $path = base_path("platform/$name");

        return file_exists($path) ? $path : null;
    }
}
