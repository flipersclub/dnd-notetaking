<?php

namespace Tests\Feature\Compendium\Location;

use App\Enums\Compendium\Location\GovernmentType;
use App\Enums\Compendium\Location\LocationType;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Location\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LocationCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $compendium = Compendium::factory()->create();
        $response = $this->postJson("/api/compendia/$compendium->id/locations");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_compendium_does_not_exist(): void
    {
        $compendium = Compendium::factory()->make();
        $response = $this->postJson("/api/compendia/$compendium->id/locations");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_create_on_compendium(): void
    {
        $user = User::factory()->create();
        $compendium = Compendium::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/compendia/$compendium->id/locations");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $compendium = Compendium::factory()->create();

        $response = $this->asAdmin()
            ->postJson("/api/compendia/$compendium->slug/locations", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseEmpty('locations');

    }

    public static function validationDataProvider()
    {
        return [
            'name not present' => [[], ['name' => 'The name field is required.']],
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 characters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 characters' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
            'start_date not a valid date' => [['start_date' => 'invalid-date'], ['start_date' => 'The start date field must be a valid date.']],
            'end_date not a valid date' => [['end_date' => 'invalid-date'], ['end_date' => 'The end date field must be a valid date.']],
            'end_date before start_date' => [['start_date' => '2023-01-01', 'end_date' => '2022-12-31'], ['end_date' => 'The end date field must be a date after or equal to start date.']],
            'game_master_id not a valid user ID' => [['game_master_id' => 999], ['game_master_id' => 'The selected game master id is invalid.']],
            'level not an integer' => [['level' => 'not-an-integer'], ['level' => 'The level field must be an integer.']],
            'level less than 1' => [['level' => 0], ['level' => 'The level field must be at least 1.']],
            'system_id not a valid system ID' => [['system_id' => 999], ['system_id' => 'The selected system id is invalid.']],
            'compendium_id not a valid compendium ID' => [['compendium_id' => 999], ['compendium_id' => 'The selected compendium id is invalid.']],
            'visibility not one of the allowed values' => [['visibility' => 'invalid-visibility'], ['visibility' => 'The selected visibility is invalid.']],
            'player_limit not an integer' => [['player_limit' => 'not-an-integer'], ['player_limit' => 'The player limit field must be an integer.']],
            'player_limit less than 1' => [['player_limit' => 0], ['player_limit' => 'The player limit field must be at least 1.']],
            'tags not an array' => [['tags' => 'not-an-array'], ['tags' => 'The tags field must be an array.']],
            'tags.* not a valid tag ID' => [['tags' => [999]], ['tags.0' => 'The selected tags.0 is invalid.']],
        ];
    }

    public function test_it_returns_successful_if_locations_returned(): void
    {
        $user = User::factory()->create();
        $compendium = Compendium::factory()->for($user, 'creator')->create();

        $payload = [
            'name' => 'Whenüa',
            'content' => Str::random(65535),
            'type_id' => LocationType::World->value,
            'demonym' => 'Whenuan',
            'population' => 1000,
            'government_type_id' => GovernmentType::Anarchy->value,
        ];

        $response = $this->actingAs($user)
            ->postJson("/api/compendia/$compendium->slug/locations?with=tags,compendium,governmentType", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'type' => [
                    'id' => LocationType::World->value,
                    'name' => LocationType::World->label(),
                ],
                'demonym' => $payload['demonym'],
                'population' => $payload['population'],
                'governmentType' => [
                    'id' => GovernmentType::Anarchy->value,
                    'name' => GovernmentType::Anarchy->label(),
                ]
            ],
        ]);

        $this->assertDatabaseHas('locations', [
            'name' => $payload['name'],
            'content' => $payload['content'],
            'type_id' => $payload['type_id'],
            'demonym' => $payload['demonym'],
            'population' => $payload['population'],
            'government_type_id' => $payload['government_type_id'],
        ]);

        $location = Location::find($response->json('data')['id']);

        $user->refresh();

        $this->assertTrue($user->can('view', $location));
        $this->assertTrue($user->can('update', $location));
        $this->assertTrue($user->can('delete', $location));
    }


}
