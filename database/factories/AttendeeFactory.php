<?php

namespace Database\Factories;

use App\Models\Attendee;
use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendee>
 */
class AttendeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'note_id' => Note::factory(),
            'user_id' => null,
            'email' => fake()->safeEmail(),
            'is_present' => fake()->boolean(),
            'is_gsr' => true,
            'title' => null,
        ];
    }

    public function present(): static
    {
        return $this->state(['is_present' => true]);
    }

    public function absent(): static
    {
        return $this->state(['is_present' => false]);
    }
}
