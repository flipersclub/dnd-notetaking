<?php

namespace Tests\Feature\Compendium\Spell;

use App\Models\Compendium\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SpellShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $spell = Spell::factory()->create();

        $response = $this->getJson("/api/spells/$spell->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_spell_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/spells/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_spell(): void
    {
        $user = User::factory()->create();

        $spell = Spell::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/spells/$spell->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_spell(): void
    {
        $spell = Spell::factory()->create();

        $response = $this->actingAs($spell->compendium->creator)
            ->getJson("/api/spells/$spell->slug?with=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $spell->name,
                'content' => $spell->content,
                'compendium' => [
                    'id' => $spell->compendium->id,
                    'name' => $spell->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_spell(): void
    {
        $spell = Spell::factory()->create();

        $user = $this->userWithPermission("spells.view.$spell->id");

        $response = $this->actingAs($user)
            ->getJson("/api/spells/$spell->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_spell(): void
    {
        $spell = Spell::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/spells/$spell->slug");

        $response->assertSuccessful();

    }
}
