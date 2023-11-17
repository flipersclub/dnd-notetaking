<?php

namespace Tests\Feature\Compendium\Character;

use App\Models\Compendium\Character;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CharacterUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $character = Character::factory()->create();

        $response = $this->putJson("/api/characters/$character->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_character_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/characters/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_character(): void
    {
        $user = User::factory()->create();

        $character = Character::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/characters/$character->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $character = Character::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/characters/$character->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('characters', [
            'id' => $character->id,
            // Ensure the original data is not modified
            'name' => $character->name,
            'content' => $character->content,
            // ... add more fields as needed
        ]);
    }

    public function test_it_returns_unprocessable_if_species_is_not_of_same_compendium(): void
    {
        $character = Character::factory()->create();

        $species = Species::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/characters/$character->slug", [
                'speciesId' => $species->id
            ]);

        $response->assertUnprocessable();

        $response->assertInvalid(['species_id' => 'The selected species id is invalid']);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 characters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'age not an integer' => [['age' => 'a string'], ['age' => 'The age field must be an integer']],
            'age too long' => [['age' => 1000000], ['age' => 'The age field must not be greater than 999999.']],
            'gender not a string' => [['gender' => ['an', 'array']], ['gender' => 'The gender field must be a string.']],
            'species_id invalid' => [['species_id' => 999999], ['species_id' => 'The selected species id is invalid.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 characters' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_character_updated_returned(): void
    {
        $character = Character::factory()->create();

        $species = Species::factory()->for($character->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'age' => 30,
            'gender' => 'Male',
            'speciesId' => $species->id,
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($character->compendium->creator)
            ->putJson("/api/characters/$character->slug?include=tags,species,species.compendium,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'age' => $payload['age'],
                'gender' => $payload['gender'],
                'content' => $payload['content'],
                'species' => [
                    'id' => $species->id,
                    'slug' => $species->slug,
                    'name' => $species->name,
                    'content' => $species->content,
                    'compendium' => [
                        'id' => $character->compendium->id,
                        'name' => $character->compendium->name
                    ]
                ],
                'compendium' => [
                    'id' => $character->compendium->id,
                    'name' => $character->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('characters', [
            'name' => $payload['name'],
            'age' => $payload['age'],
            'gender' => $payload['gender'],
            'content' => $payload['content'],
            'species_id' => $payload['speciesId'],
        ]);

        $character->refresh();

        $this->assertTrue($character->compendium->creator->can('update', $character));
        $this->assertTrue($character->compendium->creator->can('delete', $character));
    }
}
