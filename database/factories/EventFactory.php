<?php

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'date' => fake()->dateTimeBetween('-30 days', '+60 days')->format('Y-m-d'),
            'start_time' => fake()->time('H:i'),
            'end_time' => fake()->time('H:i'),
            'location' => fake()->address(),
            'status' => EventStatus::Approved,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => EventStatus::Pending]);
    }

    public function approved(): static
    {
        return $this->state(['status' => EventStatus::Approved]);
    }
}
