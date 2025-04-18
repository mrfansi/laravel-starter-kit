<?php

namespace Platform\Admin\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Admin\Models\Configuration;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Platform\Admin\Models\Configuration>
 */
class ConfigurationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Configuration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['string', 'boolean', 'integer', 'array', 'json'];
        $type = fake()->randomElement($types);
        
        return [
            'key' => fake()->unique()->word(),
            'value' => $this->generateValueForType($type),
            'group' => fake()->randomElement(['general', 'email', 'appearance', 'security']),
            'type' => $type,
            'description' => fake()->sentence(),
            'is_system' => fake()->boolean(20), // 20% chance of being a system config
            'is_public' => fake()->boolean(50), // 50% chance of being public
        ];
    }

    /**
     * Generate a value based on the configuration type.
     *
     * @param string $type
     * @return mixed
     */
    protected function generateValueForType(string $type): mixed
    {
        return match ($type) {
            'boolean' => fake()->boolean(),
            'integer' => fake()->numberBetween(1, 1000),
            'array', 'json' => json_encode([
                'key1' => fake()->word(),
                'key2' => fake()->word(),
                'key3' => fake()->word(),
            ]),
            default => fake()->sentence(3),
        };
    }

    /**
     * Indicate that the configuration is a system configuration.
     *
     * @return static
     */
    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_system' => true,
        ]);
    }

    /**
     * Indicate that the configuration is a public configuration.
     *
     * @return static
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }

    /**
     * Set the configuration type.
     *
     * @param string $type
     * @return static
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
            'value' => $this->generateValueForType($type),
        ]);
    }
}
