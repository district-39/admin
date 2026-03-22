<?php

namespace Database\Factories;

use App\Models\DistrictMeeting;
use App\Models\NoteSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NoteSection>
 */
class NoteSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'district_meeting_id' => DistrictMeeting::factory(),
            'title' => fake()->sentence(3),
            'committee' => null,
            'order' => fake()->numberBetween(1, 10),
            'json' => null,
            'text' => null,
            'markdown' => null,
        ];
    }
}
