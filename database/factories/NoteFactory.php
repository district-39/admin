<?php

namespace Database\Factories;

use App\Models\DistrictMeeting;
use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'district_meeting_id' => null,
            'file_id' => null,
        ];
    }

    public function forMeeting(?DistrictMeeting $meeting = null): static
    {
        return $this->state([
            'district_meeting_id' => $meeting?->id ?? DistrictMeeting::factory(),
        ]);
    }
}
