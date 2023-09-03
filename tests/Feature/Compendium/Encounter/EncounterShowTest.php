<?php

namespace Tests\Feature\Compendium\Encounter;

use App\Models\Compendium\Encounter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EncounterShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $encounter = Encounter::factory()->create();

        $response = $this->getJson("/api/encounters/$encounter->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_encounter_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/encounters/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_encounter(): void
    {
        $user = User::factory()->create();

        $encounter = Encounter::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/encounters/$encounter->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_encounter(): void
    {
        $encounter = Encounter::factory()->create();

        $response = $this->actingAs($encounter->compendium->creator)
            ->getJson("/api/encounters/$encounter->slug?with=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $encounter->name,
                'content' => $encounter->content,
                'compendium' => [
                    'id' => $encounter->compendium->id,
                    'name' => $encounter->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_encounter(): void
    {
        $encounter = Encounter::factory()->create();

        $user = $this->userWithPermission("encounters.view.$encounter->id");

        $response = $this->actingAs($user)
            ->getJson("/api/encounters/$encounter->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_encounter(): void
    {
        $encounter = Encounter::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/encounters/$encounter->slug");

        $response->assertSuccessful();

    }
}
