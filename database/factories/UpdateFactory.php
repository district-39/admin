<?php

namespace Database\Factories;

use App\Enums\UpdateType;
use App\Models\Update;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Update>
 */
class UpdateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'summary' => fake()->paragraph(),
            'date' => fake()->dateTimeBetween('-30 days')->format('Y-m-d'),
            'update_type' => fake()->randomElement(UpdateType::cases()),
        ];
    }
}
