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
        $response = $this->postJson("/api/compendia/$compendium->slug/locations");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_compendium_does_not_exist(): void
    {
        $compendium = Compendium::factory()->make();
        $response = $this->postJson("/api/compendia/$compendium->slug/locations");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_create_on_compendium(): void
    {
        $user = User::factory()->create();
        $compendium = Compendium::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/compendia/$compendium->slug/locations");

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
        ];
    }

    public function test_it_returns_successful_if_location_created(): void
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

    public function test_it_returns_successful_if_admin_can_create(): void
    {
        $compendium = Compendium::factory()->create();

        $payload = [
            'name' => 'Whenüa',
            'content' => Str::random(65535),
            'type_id' => LocationType::World->value
        ];

        $response = $this->asAdmin()
            ->postJson("/api/compendia/$compendium->slug/locations", $payload);

        $response->assertSuccessful();

        $this->assertDatabaseHas('locations', [
            'name' => $payload['name'],
            'compendium_id' => $compendium->id
        ]);
    }


}
