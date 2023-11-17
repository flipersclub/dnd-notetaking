<?php

namespace Tests\Feature\Compendium\NaturalResource;

use App\Models\Compendium\NaturalResource;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NaturalResourceUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $naturalResource = NaturalResource::factory()->create();

        $response = $this->putJson("/api/natural-resources/$naturalResource->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_naturalResource_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/natural-resources/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_naturalResource(): void
    {
        $user = User::factory()->create();

        $naturalResource = NaturalResource::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/natural-resources/$naturalResource->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $naturalResource = NaturalResource::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/natural-resources/$naturalResource->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('natural_resources', [
            'id' => $naturalResource->id,
            // Ensure the original data is not modified
            'name' => $naturalResource->name,
            'content' => $naturalResource->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 naturalResources' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 naturalResources' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_naturalResource_updated_returned(): void
    {
        $naturalResource = NaturalResource::factory()->create();

        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($naturalResource->compendium->creator)
            ->putJson("/api/natural-resources/$naturalResource->slug?include=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $naturalResource->compendium->id,
                    'name' => $naturalResource->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('natural_resources', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $naturalResource->refresh();

        $this->assertTrue($naturalResource->compendium->creator->can('update', $naturalResource));
        $this->assertTrue($naturalResource->compendium->creator->can('delete', $naturalResource));
    }
}
