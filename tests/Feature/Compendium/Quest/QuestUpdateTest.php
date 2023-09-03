<?php

namespace Tests\Feature\Compendium\Quest;

use App\Models\Compendium\Quest;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class QuestUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $quest = Quest::factory()->create();

        $response = $this->putJson("/api/quests/$quest->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_quest_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/quests/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_quest(): void
    {
        $user = User::factory()->create();

        $quest = Quest::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/quests/$quest->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $quest = Quest::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/quests/$quest->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('quests', [
            'id' => $quest->id,
            // Ensure the original data is not modified
            'name' => $quest->name,
            'content' => $quest->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 quests' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 quests' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_quest_updated_returned(): void
    {
        $quest = Quest::factory()->create();

        $species = Species::factory()->for($quest->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($quest->compendium->creator)
            ->putJson("/api/quests/$quest->slug?with=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $quest->compendium->id,
                    'name' => $quest->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('quests', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $quest->refresh();

        $this->assertTrue($quest->compendium->creator->can('update', $quest));
        $this->assertTrue($quest->compendium->creator->can('delete', $quest));
    }
}
