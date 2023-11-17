<?php

namespace Tests\Feature\Compendium\Encounter;

use App\Models\Compendium\Encounter;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EncounterUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $encounter = Encounter::factory()->create();

        $response = $this->putJson("/api/encounters/$encounter->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_encounter_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/encounters/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_encounter(): void
    {
        $user = User::factory()->create();

        $encounter = Encounter::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/encounters/$encounter->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $encounter = Encounter::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/encounters/$encounter->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('encounters', [
            'id' => $encounter->id,
            // Ensure the original data is not modified
            'name' => $encounter->name,
            'content' => $encounter->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 encounters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 encounters' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_encounter_updated_returned(): void
    {
        $encounter = Encounter::factory()->create();

        $species = Species::factory()->for($encounter->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($encounter->compendium->creator)
            ->putJson("/api/encounters/$encounter->slug?include=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $encounter->compendium->id,
                    'name' => $encounter->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('encounters', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $encounter->refresh();

        $this->assertTrue($encounter->compendium->creator->can('update', $encounter));
        $this->assertTrue($encounter->compendium->creator->can('delete', $encounter));
    }
}
