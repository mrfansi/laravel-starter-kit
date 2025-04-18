<?php

namespace Platform\Config\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static \Platform\Config\Models\Configuration set(string $key, mixed $value, string $group = 'general', string $type = 'string', ?string $description = null, bool $isSystem = false, bool $isPublic = false)
 * @method static bool has(string $key)
 * @method static bool delete(string $key)
 * @method static array getAllByGroup(?string $group = null)
 * @method static void import(array $configs)
 * @method static array export()
 */
class Config extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'platform.config';
    }
}
