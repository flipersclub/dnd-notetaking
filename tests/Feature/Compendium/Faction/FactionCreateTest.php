<?php

namespace Tests\Feature\Compendium\Faction;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Faction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FactionCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $compendium = Compendium::factory()->create();
        $response = $this->postJson("/api/compendia/$compendium->slug/factions");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_compendium_does_not_exist(): void
    {
        $response = $this->signedIn()
            ->postJson("/api/compendia/lalalala/factions");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_is_not_compendiums_creator(): void
    {
        $user = User::factory()->create();
        $compendium = Compendium::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/compendia/$compendium->slug/factions");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $compendium = Compendium::factory()->create();

        $response = $this->asAdmin()
            ->postJson("/api/compendia/$compendium->slug/factions", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseEmpty('factions');

    }

    public static function validationDataProvider()
    {
        return [
            'name not present' => [[], ['name' => 'The name field is required.']],
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 factions' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 factions' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_faction_created(): void
    {
        $user = User::factory()->create();
        $compendium = Compendium::factory()->for($user, 'creator')->create();

        $payload = [
            'name' => 'Dagger +1',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($user)
            ->postJson("/api/compendia/$compendium->slug/factions?include=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $compendium->id,
                    'name' => $compendium->name
                ]
            ],
        ]);

        $this->assertDatabaseHas('factions', [
            'compendium_id' => $compendium->id,
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $faction = Faction::find($response->json('data')['id']);

        $user->refresh();

        $this->assertTrue($user->can('view', $faction));
        $this->assertTrue($user->can('update', $faction));
        $this->assertTrue($user->can('delete', $faction));
    }

    public function test_it_returns_successful_if_admin_can_create(): void
    {
        $compendium = Compendium::factory()->create();

        $payload = [
            'name' => 'John Doe'
        ];

        $response = $this->asAdmin()
            ->postJson("/api/compendia/$compendium->slug/factions", $payload);

        $response->assertSuccessful();

        $this->assertDatabaseHas('factions', [
            'name' => $payload['name'],
            'compendium_id' => $compendium->id
        ]);
    }
}
