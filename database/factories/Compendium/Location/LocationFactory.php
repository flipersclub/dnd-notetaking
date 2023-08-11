<?php

namespace Database\Factories\Compendium\Location;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Location\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Compendium\Location\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(2),
            'type_id' => Type::factory(),
            'compendium_id' => Compendium::factory()
        ];
    }
}
