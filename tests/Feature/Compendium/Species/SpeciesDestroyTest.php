<?php

namespace Tests\Feature\Compendium\Species;

use App\Models\Compendium\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SpeciesDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $species = Species::factory()->create();

        $response = $this->deleteJson("/api/species/$species->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_species_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/species/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_species(): void
    {
        $user = User::factory()->create();

        $species = Species::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/species/$species->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_species_deleted(): void
    {
        $species = Species::factory()->create();

        $response = $this->actingAs($species->compendium->creator)
            ->deleteJson("/api/species/$species->slug");

        $response->assertNoContent();

        $this->assertModelMissing($species);

    }
}
