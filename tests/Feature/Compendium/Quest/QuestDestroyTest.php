<?php

namespace Tests\Feature\Compendium\Quest;

use App\Models\Compendium\Quest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuestDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $quest = Quest::factory()->create();

        $response = $this->deleteJson("/api/quests/$quest->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_quest_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/quests/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_quest(): void
    {
        $user = User::factory()->create();

        $quest = Quest::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/quests/$quest->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_quest_deleted(): void
    {
        $quest = Quest::factory()->create();

        $response = $this->actingAs($quest->compendium->creator)
            ->deleteJson("/api/quests/$quest->slug");

        $response->assertNoContent();

        $this->assertModelMissing($quest);

    }
}
