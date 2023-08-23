<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session>
 */
class SessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => \App\Models\Campaign::factory()->create()->id,
            'session_number' => $this->faker->numberBetween(1, 100),
            'name' => $this->faker->sentence,
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+1 week'),
        ];
    }
}
