<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->jobTitle();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
        ];
    }

    public function admin(): static
    {
        return $this->state([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Full administrative access',
        ]);
    }

    public function notetaker(): static
    {
        return $this->state([
            'name' => 'Notetaker',
            'slug' => 'notetaker',
            'description' => 'Meeting Notetaker',
        ]);
    }
}
