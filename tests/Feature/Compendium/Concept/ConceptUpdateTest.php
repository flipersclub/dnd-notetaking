<?php

namespace Tests\Feature\Compendium\Concept;

use App\Models\Compendium\Concept;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ConceptUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $concept = Concept::factory()->create();

        $response = $this->putJson("/api/concepts/$concept->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_concept_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/concepts/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_concept(): void
    {
        $user = User::factory()->create();

        $concept = Concept::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/concepts/$concept->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $concept = Concept::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/concepts/$concept->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('concepts', [
            'id' => $concept->id,
            // Ensure the original data is not modified
            'name' => $concept->name,
            'content' => $concept->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 concepts' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 concepts' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_concept_updated_returned(): void
    {
        $concept = Concept::factory()->create();

        $species = Species::factory()->for($concept->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($concept->compendium->creator)
            ->putJson("/api/concepts/$concept->slug?include=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $concept->compendium->id,
                    'name' => $concept->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('concepts', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $concept->refresh();

        $this->assertTrue($concept->compendium->creator->can('update', $concept));
        $this->assertTrue($concept->compendium->creator->can('delete', $concept));
    }
}
