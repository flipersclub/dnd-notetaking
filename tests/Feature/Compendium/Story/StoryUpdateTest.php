<?php

namespace Tests\Feature\Compendium\Story;

use App\Models\Compendium\Story;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoryUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $story = Story::factory()->create();

        $response = $this->putJson("/api/stories/$story->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_story_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/stories/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_story(): void
    {
        $user = User::factory()->create();

        $story = Story::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/stories/$story->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $story = Story::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/stories/$story->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('stories', [
            'id' => $story->id,
            // Ensure the original data is not modified
            'name' => $story->name,
            'content' => $story->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 stories' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 stories' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_story_updated_returned(): void
    {
        $story = Story::factory()->create();

        $species = Species::factory()->for($story->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($story->compendium->creator)
            ->putJson("/api/stories/$story->slug?with=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $story->compendium->id,
                    'name' => $story->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('stories', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $story->refresh();

        $this->assertTrue($story->compendium->creator->can('update', $story));
        $this->assertTrue($story->compendium->creator->can('delete', $story));
    }
}
