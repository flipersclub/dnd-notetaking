<?php

namespace Database\Factories\Compendium;

use App\Models\Compendium\Compendium;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Compendium\Faction>
 */
class FactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'compendium_id' => Compendium::factory(),
            'name' => $this->faker->words(),
            'content' => $this->faker->sentence(5),
        ];
    }
}
