<?php

namespace Tests\Feature\Compendium\Pantheon;

use App\Models\Compendium\Pantheon;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PantheonUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $pantheon = Pantheon::factory()->create();

        $response = $this->putJson("/api/pantheons/$pantheon->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_pantheon_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/pantheons/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_pantheon(): void
    {
        $user = User::factory()->create();

        $pantheon = Pantheon::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/pantheons/$pantheon->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $pantheon = Pantheon::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/pantheons/$pantheon->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('pantheons', [
            'id' => $pantheon->id,
            // Ensure the original data is not modified
            'name' => $pantheon->name,
            'content' => $pantheon->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 pantheons' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 pantheons' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_pantheon_updated_returned(): void
    {
        $pantheon = Pantheon::factory()->create();

        $species = Species::factory()->for($pantheon->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($pantheon->compendium->creator)
            ->putJson("/api/pantheons/$pantheon->slug?include=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $pantheon->compendium->id,
                    'name' => $pantheon->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('pantheons', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $pantheon->refresh();

        $this->assertTrue($pantheon->compendium->creator->can('update', $pantheon));
        $this->assertTrue($pantheon->compendium->creator->can('delete', $pantheon));
    }
}
