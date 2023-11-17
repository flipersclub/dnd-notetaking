<?php

namespace Tests\Feature\Compendium\Plane;

use App\Models\Compendium\Plane;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PlaneUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $plane = Plane::factory()->create();

        $response = $this->putJson("/api/planes/$plane->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_plane_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/planes/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_plane(): void
    {
        $user = User::factory()->create();

        $plane = Plane::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/planes/$plane->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $plane = Plane::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/planes/$plane->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('planes', [
            'id' => $plane->id,
            // Ensure the original data is not modified
            'name' => $plane->name,
            'content' => $plane->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 planes' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 planes' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_plane_updated_returned(): void
    {
        $plane = Plane::factory()->create();

        $species = Species::factory()->for($plane->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($plane->compendium->creator)
            ->putJson("/api/planes/$plane->slug?include=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $plane->compendium->id,
                    'name' => $plane->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('planes', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $plane->refresh();

        $this->assertTrue($plane->compendium->creator->can('update', $plane));
        $this->assertTrue($plane->compendium->creator->can('delete', $plane));
    }
}
