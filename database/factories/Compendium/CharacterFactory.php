<?php

namespace Database\Factories\Compendium;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Compendium\Character>
 */
class CharacterFactory extends Factory
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
            'name' => $this->faker->name(),
            'age' => $this->faker->numberBetween(1, 999),
            'gender' => $this->faker->randomElement(['male', 'female', 'non-binary']),
            'species_id' => fn($attributes) => Species::factory()->state(['compendium_id' => $attributes['compendium_id']]),
            'content' => $this->faker->sentence(5),
        ];
    }
}
