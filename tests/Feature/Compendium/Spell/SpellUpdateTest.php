<?php

namespace Tests\Feature\Compendium\Spell;

use App\Models\Compendium\Spell;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SpellUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $spell = Spell::factory()->create();

        $response = $this->putJson("/api/spells/$spell->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_spell_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/spells/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_spell(): void
    {
        $user = User::factory()->create();

        $spell = Spell::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/spells/$spell->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $spell = Spell::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/spells/$spell->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('spells', [
            'id' => $spell->id,
            // Ensure the original data is not modified
            'name' => $spell->name,
            'content' => $spell->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 spells' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 spells' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_spell_updated_returned(): void
    {
        $spell = Spell::factory()->create();

        $species = Species::factory()->for($spell->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($spell->compendium->creator)
            ->putJson("/api/spells/$spell->slug?include=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $spell->compendium->id,
                    'name' => $spell->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('spells', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $spell->refresh();

        $this->assertTrue($spell->compendium->creator->can('update', $spell));
        $this->assertTrue($spell->compendium->creator->can('delete', $spell));
    }
}
