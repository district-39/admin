<?php

namespace Database\Factories;

use App\Enums\EmailStatus;
use App\Models\DistrictEmail;
use App\Models\DistrictMeeting;
use App\Models\Meeting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DistrictEmail>
 */
class DistrictEmailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(4),
            'content' => fake()->paragraphs(3, true),
            'to' => fake()->safeEmail(),
            'from' => fake()->safeEmail(),
            'status' => EmailStatus::Draft,
            'type' => 'district_email',
            'meeting_id' => Meeting::factory(),
            'district_meeting_id' => DistrictMeeting::factory(),
        ];
    }
}
