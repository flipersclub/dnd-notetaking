<?php

namespace Tests\Feature\Compendium\Faction;

use App\Models\Compendium\Faction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FactionShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $faction = Faction::factory()->create();

        $response = $this->getJson("/api/factions/$faction->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_faction_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/factions/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_faction(): void
    {
        $user = User::factory()->create();

        $faction = Faction::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/factions/$faction->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_faction(): void
    {
        $faction = Faction::factory()->create();

        $response = $this->actingAs($faction->compendium->creator)
            ->getJson("/api/factions/$faction->slug?with=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $faction->name,
                'content' => $faction->content,
                'compendium' => [
                    'id' => $faction->compendium->id,
                    'name' => $faction->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_faction(): void
    {
        $faction = Faction::factory()->create();

        $user = $this->userWithPermission("factions.view.$faction->id");

        $response = $this->actingAs($user)
            ->getJson("/api/factions/$faction->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_faction(): void
    {
        $faction = Faction::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/factions/$faction->slug");

        $response->assertSuccessful();

    }
}
