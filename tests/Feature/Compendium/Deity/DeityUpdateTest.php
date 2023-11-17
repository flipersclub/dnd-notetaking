<?php

namespace Tests\Feature\Compendium\Deity;

use App\Models\Compendium\Deity;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeityUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $deity = Deity::factory()->create();

        $response = $this->putJson("/api/deities/$deity->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_deity_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/deities/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_deity(): void
    {
        $user = User::factory()->create();

        $deity = Deity::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/deities/$deity->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $deity = Deity::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/deities/$deity->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('deities', [
            'id' => $deity->id,
            // Ensure the original data is not modified
            'name' => $deity->name,
            'content' => $deity->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 deities' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 deities' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_deity_updated_returned(): void
    {
        $deity = Deity::factory()->create();

        $species = Species::factory()->for($deity->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($deity->compendium->creator)
            ->putJson("/api/deities/$deity->slug?include=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $deity->compendium->id,
                    'name' => $deity->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('deities', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $deity->refresh();

        $this->assertTrue($deity->compendium->creator->can('update', $deity));
        $this->assertTrue($deity->compendium->creator->can('delete', $deity));
    }
}
