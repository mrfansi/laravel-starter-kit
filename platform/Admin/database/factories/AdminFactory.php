<?php

namespace Platform\Admin\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Platform\Admin\Models\Admin;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Platform\Admin\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Admin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Default password for testing
            'remember_token' => Str::random(10),
            'is_active' => true,
            'last_login_at' => fake()->optional()->dateTimeThisMonth(),
        ];
    }

    /**
     * Indicate that the admin is inactive.
     *
     * @return static
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
