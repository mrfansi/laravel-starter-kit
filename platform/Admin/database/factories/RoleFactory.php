<?php

namespace Platform\Admin\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Admin\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Platform\Admin\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();
        return [
            'name' => strtolower($name),
            'display_name' => ucfirst($name),
            'description' => fake()->sentence(),
        ];
    }

    /**
     * Indicate that the role is for administrators.
     *
     * @return static
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Administrator with full access',
        ]);
    }
}
