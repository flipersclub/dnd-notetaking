<?php

namespace Tests\Feature\Compendium\Location;

use App\Enums\Compendium\Location\GovernmentType;
use App\Enums\Compendium\Location\LocationType;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Location\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $compendium = Compendium::factory()->create();
        $response = $this->getJson("/api/compendia/$compendium->slug/locations");

        $response->assertUnauthorized();
    }

    public function test_it_returns_unauthorized_if_user_hasnt_created_a_compendium(): void
    {
        $compendium = Compendium::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/compendia/$compendium->slug/locations");

        $response->assertForbidden();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_see_for_this_compendium(): void
    {
        $compendium = Compendium::factory()->create();
        $user = User::factory()->hasCompendia(3)->create();

        $response = $this->actingAs($user)
            ->getJson("/api/compendia/$compendium->slug/locations");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_locations_returned(): void
    {
        $compendium = Compendium::factory()->create();

        $dontSeeLocations = Location::factory(10)->create();
        $seeLocations = Location::factory(10)
            ->for($compendium)
            ->forGovernmentType()
            ->create();

        $response = $this->actingAs($compendium->creator)
            ->getJson("/api/compendia/$compendium->slug/locations?with=governmentType");

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');

        $response->assertJson([
            'data' => $seeLocations->map(fn(Location $location) => [
                'name' => $location->name,
                'content' => $location->content,
                'type' => [
                    'id' => $location->type->id,
                    'name' => $location->type->name,
                ],
                'demonym' => $location->demonym,
                'population' => $location->population,
                'governmentType' => [
                    'id' => $location->governmentType->id,
                    'name' => $location->governmentType->name,
                ]
            ])->toArray()
        ]);
    }

    public function test_admin_can_see_other_user_compendiums(): void
    {
        $compendium = Compendium::factory()->create();

        $dontSeeLocations = Location::factory(10)->create();
        $seeLocations = Location::factory(10)->for($compendium)->create();

        $response = $this->asAdmin()
            ->getJson("/api/compendia/$compendium->slug/locations");

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');
    }
}
