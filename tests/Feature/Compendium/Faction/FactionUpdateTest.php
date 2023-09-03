<?php

namespace Tests\Feature\Compendium\Faction;

use App\Models\Compendium\Faction;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FactionUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $faction = Faction::factory()->create();

        $response = $this->putJson("/api/factions/$faction->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_faction_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/factions/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_faction(): void
    {
        $user = User::factory()->create();

        $faction = Faction::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/factions/$faction->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $faction = Faction::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/factions/$faction->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('factions', [
            'id' => $faction->id,
            // Ensure the original data is not modified
            'name' => $faction->name,
            'content' => $faction->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 factions' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 factions' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_faction_updated_returned(): void
    {
        $faction = Faction::factory()->create();

        $species = Species::factory()->for($faction->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($faction->compendium->creator)
            ->putJson("/api/factions/$faction->slug?with=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $faction->compendium->id,
                    'name' => $faction->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('factions', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $faction->refresh();

        $this->assertTrue($faction->compendium->creator->can('update', $faction));
        $this->assertTrue($faction->compendium->creator->can('delete', $faction));
    }
}
