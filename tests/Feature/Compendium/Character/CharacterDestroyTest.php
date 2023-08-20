<?php

namespace Tests\Feature\Compendium\Character;

use App\Models\Compendium\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CharacterDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $character = Character::factory()->create();

        $response = $this->deleteJson("/api/characters/$character->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_character_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/characters/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_character(): void
    {
        $user = User::factory()->create();

        $character = Character::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/characters/$character->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_character_deleted(): void
    {
        $character = Character::factory()->create();

        $response = $this->actingAs($character->compendium->creator)
            ->deleteJson("/api/characters/$character->slug");

        $response->assertNoContent();

        $this->assertModelMissing($character);

    }
}
