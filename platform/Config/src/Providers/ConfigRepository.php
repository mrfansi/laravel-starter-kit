<?php

namespace Platform\Config\Providers;

use Illuminate\Config\Repository;
use Platform\Config\Services\ConfigService;

class ConfigRepository extends Repository
{
    /**
     * The config service instance.
     *
     * @var \Platform\Config\Services\ConfigService
     */
    protected $configService;

    /**
     * Create a new configuration repository.
     *
     * @param  \Platform\Config\Services\ConfigService  $configService
     * @return void
     */
    public function __construct(ConfigService $configService)
    {
        parent::__construct([]);
        $this->configService = $configService;
    }

    /**
     * Get the specified configuration value.
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        // First check if the configuration exists in the database
        if ($this->configService->has($key)) {
            return $this->configService->get($key);
        }

        // If not, fall back to the parent implementation (env and config files)
        return parent::get($key, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return void
     */
    public function set($key, $value = null)
    {
        // Set the value in both the database and the parent repository
        $this->configService->set($key, $value);
        parent::set($key, $value);
    }
}
