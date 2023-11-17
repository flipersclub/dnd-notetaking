<?php

namespace Tests\Feature\Compendium\Species;

use App\Models\Compendium\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SpeciesShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $species = Species::factory()->create();

        $response = $this->getJson("/api/species/$species->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_species_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/species/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_species(): void
    {
        $user = User::factory()->create();

        $species = Species::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/species/$species->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_species(): void
    {
        $species = Species::factory()->create();

        $response = $this->actingAs($species->compendium->creator)
            ->getJson("/api/species/$species->slug?include=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $species->name,
                'content' => $species->content,
                'compendium' => [
                    'id' => $species->compendium->id,
                    'name' => $species->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_species(): void
    {
        $species = Species::factory()->create();

        $user = $this->userWithPermission("species.view.$species->id");

        $response = $this->actingAs($user)
            ->getJson("/api/species/$species->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_species(): void
    {
        $species = Species::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/species/$species->slug");

        $response->assertSuccessful();

    }
}
