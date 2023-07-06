<?php

namespace Tests\Feature\Models\Compendium\Location;

use App\Models\Compendium\Location\Location;
use App\Models\Compendium\Location\Size;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SizeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_it_can_have_locations(): void
    {
        $size = Size::factory()->hasLocations(3)->create();

        $this->assertCount(3, $size->locations);

        foreach ($size->locations as $location) {
            $this->assertInstanceOf(Location::class, $location);
        }

        $sizeWithoutLocations = Size::factory()->create();

        $this->assertCount(0, $sizeWithoutLocations->locations);
    }
}
