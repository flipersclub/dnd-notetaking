<?php

namespace Tests\Feature\Compendium\Plane;

use App\Models\Compendium\Plane;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlaneShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $plane = Plane::factory()->create();

        $response = $this->getJson("/api/planes/$plane->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_plane_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/planes/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_plane(): void
    {
        $user = User::factory()->create();

        $plane = Plane::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/planes/$plane->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_plane(): void
    {
        $plane = Plane::factory()->create();

        $response = $this->actingAs($plane->compendium->creator)
            ->getJson("/api/planes/$plane->slug?include=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $plane->name,
                'content' => $plane->content,
                'compendium' => [
                    'id' => $plane->compendium->id,
                    'name' => $plane->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_plane(): void
    {
        $plane = Plane::factory()->create();

        $user = $this->userWithPermission("planes.view.$plane->id");

        $response = $this->actingAs($user)
            ->getJson("/api/planes/$plane->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_plane(): void
    {
        $plane = Plane::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/planes/$plane->slug");

        $response->assertSuccessful();

    }
}
