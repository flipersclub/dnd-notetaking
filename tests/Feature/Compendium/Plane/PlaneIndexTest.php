<?php

namespace Tests\Feature\Compendium\Plane;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Plane;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaneIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $compendium = Compendium::factory()->create();
        $response = $this->getJson("/api/compendia/$compendium->slug/planes");

        $response->assertUnauthorized();
    }

    public function test_it_returns_forbidden_if_user_hasnt_created_a_compendium(): void
    {
        $compendium = Compendium::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/compendia/$compendium->slug/planes");

        $response->assertForbidden();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_for_this_compendium(): void
    {
        $compendium = Compendium::factory()->create();
        $user = User::factory()->hasCompendia(3)->create();

        $response = $this->actingAs($user)
            ->getJson("/api/compendia/$compendium->slug/planes");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_planes_returned(): void
    {
        $compendium = Compendium::factory()->create();

        $dontSeePlanes = Plane::factory(10)->create();
        $seePlanes = Plane::factory(10)
            ->for($compendium)
            ->create();

        $response = $this->actingAs($compendium->creator)
            ->getJson("/api/compendia/$compendium->slug/planes?with=compendium");

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');

        $response->assertJson([
            'data' => $seePlanes->map(fn(Plane $plane) => [
                'name' => $plane->name,
                'content' => $plane->content,
                'compendium' => [
                    'id' => $compendium->id,
                    'name' => $compendium->name
                ]
            ])->toArray()
        ]);
    }

    public function test_admin_can_see_other_user_compendiums(): void
    {
        $compendium = Compendium::factory()->create();

        $dontSeePlanes = Plane::factory(10)->create();
        $seePlanes = Plane::factory(10)->for($compendium)->create();

        $response = $this->asAdmin()
            ->getJson("/api/compendia/$compendium->slug/planes");

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');
    }
}
