<?php

namespace Tests\Feature\Compendium\Encounter;

use App\Models\Compendium\Encounter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EncounterDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $encounter = Encounter::factory()->create();

        $response = $this->deleteJson("/api/encounters/$encounter->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_encounter_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/encounters/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_encounter(): void
    {
        $user = User::factory()->create();

        $encounter = Encounter::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/encounters/$encounter->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_encounter_deleted(): void
    {
        $encounter = Encounter::factory()->create();

        $response = $this->actingAs($encounter->compendium->creator)
            ->deleteJson("/api/encounters/$encounter->slug");

        $response->assertNoContent();

        $this->assertModelMissing($encounter);

    }
}
