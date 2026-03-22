<?php

namespace Database\Factories;

use App\Enums\EmailStatus;
use App\Models\Email;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Email>
 */
class EmailFactory extends Factory
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
            'cc' => null,
            'bcc' => null,
            'status' => EmailStatus::Draft,
            'date_sent' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state([
            'status' => EmailStatus::Sent,
            'date_sent' => fake()->dateTimeBetween('-30 days'),
        ]);
    }
}
