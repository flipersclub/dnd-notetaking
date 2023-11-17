<?php

namespace Tests\Feature\Compendium\Location;

use App\Enums\Compendium\Location\GovernmentType;
use App\Enums\Compendium\Location\LocationType;
use App\Models\Compendium\Location\Location;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LocationUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $location = Location::factory()->create();

        $response = $this->putJson("/api/locations/$location->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_location_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/locations/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_update_location(): void
    {
        $user = User::factory()->create();

        $location = Location::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/locations/$location->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $location = Location::factory()->create();

        $response = $this->asAdmin()
            ->putJson('/api/locations/' . $location->slug, $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            // Ensure the original data is not modified
            'name' => $location->name,
            'content' => $location->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 characters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 characters' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_location_updated_returned(): void
    {
        $tags = Tag::factory()->count(2)->create();

        $payload = [
            'name' => 'Whenüa',
            'content' => Str::random(65535),
            'type_id' => LocationType::World->value,
            'demonym' => 'Whenuan',
            'population' => 1000,
            'government_type_id' => GovernmentType::Anarchy->value,
            'tags' => $tags->pluck('id')
        ];

        $location = Location::factory()->create();

        $response = $this->actingAs($location->compendium->creator)
            ->putJson("/api/locations/{$location->slug}?include=tags,compendium,governmentType", $payload);

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
                ],
                'tags' => $tags->map(fn($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name
                ])->toArray()
            ]
        ]);

        $this->assertDatabaseHas('locations', [
            'name' => $payload['name'],
            'content' => $payload['content'],
            'type_id' => $payload['type_id'],
            'demonym' => $payload['demonym'],
            'population' => $payload['population'],
            'government_type_id' => $payload['government_type_id'],
        ]);

        $location->refresh();

        $this->assertTrue($location->compendium->creator->can('update', $location));
        $this->assertTrue($location->compendium->creator->can('delete', $location));

        // Assert tags
        $this->assertEquals($tags->pluck('id'), $location->tags->pluck('id'));
    }
}
