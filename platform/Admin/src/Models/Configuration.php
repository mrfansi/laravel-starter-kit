<?php

namespace Platform\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;
    
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Platform\Admin\Database\Factories\ConfigurationFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
        'is_system',
        'is_public',
    ];

    /**
     * Cast the value based on the type attribute.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        return match ($this->type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'array', 'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Format the value based on the type attribute before saving.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setValueAttribute($value): void
    {
        if (is_array($value) || is_object($value)) {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = match ($this->attributes['type'] ?? 'string') {
                'array', 'json' => is_string($value) ? $value : json_encode($value),
                'boolean' => (string) (bool) $value,
                'integer' => (string) (int) $value,
                default => (string) $value,
            };
        }
    }
}
