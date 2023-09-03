<?php

namespace Tests\Feature\Compendium\Story;

use App\Models\Compendium\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoryDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $story = Story::factory()->create();

        $response = $this->deleteJson("/api/stories/$story->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_story_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/stories/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_story(): void
    {
        $user = User::factory()->create();

        $story = Story::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/stories/$story->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_story_deleted(): void
    {
        $story = Story::factory()->create();

        $response = $this->actingAs($story->compendium->creator)
            ->deleteJson("/api/stories/$story->slug");

        $response->assertNoContent();

        $this->assertModelMissing($story);

    }
}
