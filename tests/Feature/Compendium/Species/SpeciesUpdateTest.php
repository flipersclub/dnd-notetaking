<?php

namespace Tests\Feature\Compendium\Species;

use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SpeciesUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $species = Species::factory()->create();

        $response = $this->putJson("/api/species/$species->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_species_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/species/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_species(): void
    {
        $user = User::factory()->create();

        $species = Species::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/species/$species->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $species = Species::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/species/$species->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('species', [
            'id' => $species->id,
            // Ensure the original data is not modified
            'name' => $species->name,
            'content' => $species->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 species' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 species' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_species_updated_returned(): void
    {
        $species = Species::factory()->create();

        $species = Species::factory()->for($species->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($species->compendium->creator)
            ->putJson("/api/species/$species->slug?with=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $species->compendium->id,
                    'name' => $species->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('species', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $species->refresh();

        $this->assertTrue($species->compendium->creator->can('update', $species));
        $this->assertTrue($species->compendium->creator->can('delete', $species));
    }
}
