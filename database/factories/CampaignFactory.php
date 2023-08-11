<?php

namespace Database\Factories;

use App\Enums\CampaignVisibility;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'active' => $this->faker->boolean(),
            'visibility' => $this->faker->randomElement(CampaignVisibility::cases()),
            'game_master_id' => User::factory()
        ];
    }
}
