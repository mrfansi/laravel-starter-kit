<?php

namespace Platform\Core\Support;

use Illuminate\Support\Facades\File;

class ModuleDiscovery
{
    /**
     * Discover all modules
     */
    public static function discover(): array
    {
        $modules = [];
        $modulesPath = base_path('platform');

        if (! File::exists($modulesPath)) {
            return $modules;
        }

        $directories = File::directories($modulesPath);

        foreach ($directories as $directory) {
            $composerPath = "$directory/composer.json";

            if (File::exists($composerPath)) {
                $composerJson = json_decode(File::get($composerPath), true);

                if (isset($composerJson['extra']['laravel']['providers'])) {
                    foreach ($composerJson['extra']['laravel']['providers'] as $provider) {
                        $modules[basename($directory)] = $provider;
                    }
                }
            }
        }

        return $modules;
    }
}
