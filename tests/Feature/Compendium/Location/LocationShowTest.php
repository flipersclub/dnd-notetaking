<?php

namespace Tests\Feature\Compendium\Location;

use App\Enums\Compendium\Location\LocationType;
use App\Models\Compendium\Location\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LocationShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $location = Location::factory()->create();

        $response = $this->getJson("/api/locations/$location->id");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_location_not_existent(): void
    {
        $user = User::factory()->create();

        $location = Location::factory()->make();

        $response = $this->actingAs($user)
            ->getJson("/api/locations/$location->slug");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_location(): void
    {
        $user = User::factory()->create();

        $location = Location::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/locations/$location->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_location(): void
    {
        $location = Location::factory()->create();

        $response = $this->actingAs($location->compendium->creator)
            ->getJson("/api/locations/$location->slug");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'id' => $location->id,
                'slug' => $location->slug,
                'name' => $location->name,
                'content' => $location->content,
                'type' => [
                    'id' => $location->type->id,
                    'name' => $location->type->name,
                ],
                'demonym' => $location->demonym,
                'population' => $location->population,
            ]
        ]);

    }

    public function test_user_with_permission_can_see_location(): void
    {
        $location = Location::factory()->create();

        $user = $this->userWithPermission("locations.view.$location->id");

        $response = $this->actingAs($user)
            ->getJson("/api/locations/$location->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_location(): void
    {
        $location = Location::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/locations/$location->slug");

        $response->assertSuccessful();

    }
}
