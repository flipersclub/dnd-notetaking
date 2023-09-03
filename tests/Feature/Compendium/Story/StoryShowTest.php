<?php

namespace Tests\Feature\Compendium\Story;

use App\Models\Compendium\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoryShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $story = Story::factory()->create();

        $response = $this->getJson("/api/stories/$story->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_story_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/stories/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_story(): void
    {
        $user = User::factory()->create();

        $story = Story::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/stories/$story->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_story(): void
    {
        $story = Story::factory()->create();

        $response = $this->actingAs($story->compendium->creator)
            ->getJson("/api/stories/$story->slug?with=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $story->name,
                'content' => $story->content,
                'compendium' => [
                    'id' => $story->compendium->id,
                    'name' => $story->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_story(): void
    {
        $story = Story::factory()->create();

        $user = $this->userWithPermission("stories.view.$story->id");

        $response = $this->actingAs($user)
            ->getJson("/api/stories/$story->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_story(): void
    {
        $story = Story::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/stories/$story->slug");

        $response->assertSuccessful();

    }
}
