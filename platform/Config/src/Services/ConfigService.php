<?php

namespace Platform\Config\Services;

use Illuminate\Support\Facades\Cache;
use Platform\Config\Models\Configuration;

class ConfigService
{
    /**
     * Cache key prefix for configuration values.
     *
     * @var string
     */
    protected string $cachePrefix = 'config_';

    /**
     * Cache expiration time in seconds.
     *
     * @var int
     */
    protected int $cacheExpiration = 86400; // 24 hours

    /**
     * Get a configuration value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember($this->cachePrefix . $key, $this->cacheExpiration, function () use ($key, $default) {
            $config = Configuration::where('key', $key)->first();
            return $config ? $config->value : $default;
        });
    }

    /**
     * Set a configuration value.
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string $type
     * @param string|null $description
     * @param bool $isSystem
     * @param bool $isPublic
     * @return Configuration
     */
    public function set(
        string $key,
        mixed $value,
        string $group = 'general',
        string $type = 'string',
        ?string $description = null,
        bool $isSystem = false,
        bool $isPublic = false
    ): Configuration {
        $config = Configuration::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
                'description' => $description,
                'is_system' => $isSystem,
                'is_public' => $isPublic,
            ]
        );

        // Clear the cache for this key
        Cache::forget($this->cachePrefix . $key);

        return $config;
    }

    /**
     * Check if a configuration key exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return Configuration::where('key', $key)->exists();
    }

    /**
     * Delete a configuration key.
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        $deleted = Configuration::where('key', $key)->delete();
        
        // Clear the cache for this key
        Cache::forget($this->cachePrefix . $key);
        
        return (bool) $deleted;
    }

    /**
     * Get all configurations by group.
     *
     * @param string|null $group
     * @return array
     */
    public function getAllByGroup(?string $group = null): array
    {
        $query = Configuration::query();
        
        if ($group) {
            $query->where('group', $group);
        }
        
        $configs = $query->get();
        
        $result = [];
        foreach ($configs as $config) {
            $result[$config->key] = $config->value;
        }
        
        return $result;
    }

    /**
     * Import configurations from array.
     *
     * @param array $configs
     * @return void
     */
    public function import(array $configs): void
    {
        foreach ($configs as $key => $config) {
            $this->set(
                $key,
                $config['value'] ?? null,
                $config['group'] ?? 'general',
                $config['type'] ?? 'string',
                $config['description'] ?? null,
                $config['is_system'] ?? false,
                $config['is_public'] ?? false
            );
        }
    }

    /**
     * Export all configurations as array.
     *
     * @return array
     */
    public function export(): array
    {
        $configs = Configuration::all();
        
        $result = [];
        foreach ($configs as $config) {
            $result[$config->key] = [
                'value' => $config->value,
                'group' => $config->group,
                'type' => $config->type,
                'description' => $config->description,
                'is_system' => $config->is_system,
                'is_public' => $config->is_public,
            ];
        }
        
        return $result;
    }
}
