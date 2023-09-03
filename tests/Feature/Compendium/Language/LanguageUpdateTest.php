<?php

namespace Tests\Feature\Compendium\Language;

use App\Models\Compendium\Language;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LanguageUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $language = Language::factory()->create();

        $response = $this->putJson("/api/languages/$language->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_language_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/languages/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_language(): void
    {
        $user = User::factory()->create();

        $language = Language::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/languages/$language->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $language = Language::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/languages/$language->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('languages', [
            'id' => $language->id,
            // Ensure the original data is not modified
            'name' => $language->name,
            'content' => $language->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 languages' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 languages' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_language_updated_returned(): void
    {
        $language = Language::factory()->create();

        $species = Species::factory()->for($language->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($language->compendium->creator)
            ->putJson("/api/languages/$language->slug?with=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $language->compendium->id,
                    'name' => $language->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('languages', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $language->refresh();

        $this->assertTrue($language->compendium->creator->can('update', $language));
        $this->assertTrue($language->compendium->creator->can('delete', $language));
    }
}
