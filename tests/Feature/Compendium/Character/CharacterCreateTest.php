<?php

namespace Tests\Feature\Compendium\Character;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Character;
use App\Models\Compendium\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CharacterCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $compendium = Compendium::factory()->create();
        $response = $this->postJson("/api/compendia/$compendium->slug/characters");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_compendium_does_not_exist(): void
    {
        $response = $this->signedIn()
            ->postJson("/api/compendia/lalalala/characters");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_is_not_compendiums_creator(): void
    {
        $user = User::factory()->create();
        $compendium = Compendium::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/compendia/$compendium->slug/characters");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $compendium = Compendium::factory()->create();

        $response = $this->asAdmin()
            ->postJson("/api/compendia/$compendium->slug/characters", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseEmpty('characters');

    }

    public static function validationDataProvider()
    {
        return [
            'name not present' => [[], ['name' => 'The name field is required.']],
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

    public function test_it_returns_unprocessable_if_species_is_not_of_same_compendium(): void
    {
        $compendium = Compendium::factory()->create();
        $species = Species::factory()->create();

        $response = $this->asAdmin()
            ->postJson("/api/compendia/$compendium->slug/characters", [
                'speciesId' => $species->id
            ]);

        $response->assertUnprocessable();

        $response->assertInvalid(['species_id' => 'The selected species id is invalid']);
    }

    public function test_it_returns_successful_if_character_created(): void
    {
        $user = User::factory()->create();
        $compendium = Compendium::factory()->for($user, 'creator')->create();

        $species = Species::factory()->for($compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'age' => 30,
            'gender' => 'Male',
            'speciesId' => $species->id,
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($user)
            ->postJson("/api/compendia/$compendium->slug/characters?include=tags,species,species.compendium,compendium", $payload);

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
                        'id' => $compendium->id,
                        'name' => $compendium->name
                    ]
                ],
                'compendium' => [
                    'id' => $compendium->id,
                    'name' => $compendium->name
                ]
            ],
        ]);

        $this->assertDatabaseHas('characters', [
            'compendium_id' => $compendium->id,
            'name' => $payload['name'],
            'age' => $payload['age'],
            'gender' => $payload['gender'],
            'content' => $payload['content'],
            'species_id' => $payload['speciesId'],
        ]);

        $character = Character::find($response->json('data')['id']);

        $user->refresh();

        $this->assertTrue($user->can('view', $character));
        $this->assertTrue($user->can('update', $character));
        $this->assertTrue($user->can('delete', $character));
    }

    public function test_it_returns_successful_if_admin_can_create(): void
    {
        $compendium = Compendium::factory()->create();

        $payload = [
            'name' => 'John Doe'
        ];

        $response = $this->asAdmin()
            ->postJson("/api/compendia/$compendium->slug/characters", $payload);

        $response->assertSuccessful();

        $this->assertDatabaseHas('characters', [
            'name' => $payload['name'],
            'compendium_id' => $compendium->id
        ]);
    }
}
