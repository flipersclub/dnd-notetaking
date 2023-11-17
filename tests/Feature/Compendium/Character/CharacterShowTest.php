<?php

namespace Tests\Feature\Compendium\Character;

use App\Models\Compendium\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CharacterShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $character = Character::factory()->create();

        $response = $this->getJson("/api/characters/$character->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_character_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/characters/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_character(): void
    {
        $user = User::factory()->create();

        $character = Character::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/characters/$character->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_character(): void
    {
        $character = Character::factory()->create();

        $response = $this->actingAs($character->compendium->creator)
            ->getJson("/api/characters/$character->slug?include=species,species.compendium,compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $character->name,
                'age' => $character->age,
                'gender' => $character->gender,
                'species' => [
                    'id' => $character->species->id,
                    'slug' => $character->species->slug,
                    'name' => $character->species->name,
                    'content' => $character->species->content,
                    'compendium' => [
                        'id' => $character->compendium->id,
                        'name' => $character->compendium->name
                    ]
                ],
                'content' => $character->content,
                'compendium' => [
                    'id' => $character->compendium->id,
                    'name' => $character->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_character(): void
    {
        $character = Character::factory()->create();

        $user = $this->userWithPermission("characters.view.$character->id");

        $response = $this->actingAs($user)
            ->getJson("/api/characters/$character->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_character(): void
    {
        $character = Character::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/characters/$character->slug");

        $response->assertSuccessful();

    }
}
