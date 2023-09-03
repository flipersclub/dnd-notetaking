<?php

namespace Tests\Feature\Compendium\Plane;

use App\Models\Compendium\Plane;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlaneDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $plane = Plane::factory()->create();

        $response = $this->deleteJson("/api/planes/$plane->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_plane_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/planes/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_plane(): void
    {
        $user = User::factory()->create();

        $plane = Plane::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/planes/$plane->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_plane_deleted(): void
    {
        $plane = Plane::factory()->create();

        $response = $this->actingAs($plane->compendium->creator)
            ->deleteJson("/api/planes/$plane->slug");

        $response->assertNoContent();

        $this->assertModelMissing($plane);

    }
}
