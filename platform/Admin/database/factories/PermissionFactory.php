<?php

namespace Platform\Admin\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Admin\Models\Permission;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Platform\Admin\Models\Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Permission::class;

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
            'group' => fake()->randomElement(['users', 'roles', 'permissions', 'settings']),
        ];
    }

    /**
     * Indicate that the permission is for a specific group.
     *
     * @param string $group
     * @return static
     */
    public function forGroup(string $group): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => $group,
        ]);
    }
}
