<?php

namespace Tests\Feature\Compendium\Quest;

use App\Models\Compendium\Quest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuestShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $quest = Quest::factory()->create();

        $response = $this->getJson("/api/quests/$quest->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_quest_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/quests/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_quest(): void
    {
        $user = User::factory()->create();

        $quest = Quest::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/quests/$quest->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_quest(): void
    {
        $quest = Quest::factory()->create();

        $response = $this->actingAs($quest->compendium->creator)
            ->getJson("/api/quests/$quest->slug?include=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $quest->name,
                'content' => $quest->content,
                'compendium' => [
                    'id' => $quest->compendium->id,
                    'name' => $quest->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_quest(): void
    {
        $quest = Quest::factory()->create();

        $user = $this->userWithPermission("quests.view.$quest->id");

        $response = $this->actingAs($user)
            ->getJson("/api/quests/$quest->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_quest(): void
    {
        $quest = Quest::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/quests/$quest->slug");

        $response->assertSuccessful();

    }
}
