<?php

namespace Tests\Feature\Compendium\Faction;

use App\Models\Compendium\Faction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FactionDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $faction = Faction::factory()->create();

        $response = $this->deleteJson("/api/factions/$faction->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_faction_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/factions/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_faction(): void
    {
        $user = User::factory()->create();

        $faction = Faction::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/factions/$faction->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_faction_deleted(): void
    {
        $faction = Faction::factory()->create();

        $response = $this->actingAs($faction->compendium->creator)
            ->deleteJson("/api/factions/$faction->slug");

        $response->assertNoContent();

        $this->assertModelMissing($faction);

    }
}
