<?php

namespace Tests\Feature\Compendium\Religion;

use App\Models\Compendium\Religion;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReligionUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $religion = Religion::factory()->create();

        $response = $this->putJson("/api/religions/$religion->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_religion_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/religions/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_religion(): void
    {
        $user = User::factory()->create();

        $religion = Religion::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/religions/$religion->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $religion = Religion::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/religions/$religion->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('religions', [
            'id' => $religion->id,
            // Ensure the original data is not modified
            'name' => $religion->name,
            'content' => $religion->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 religions' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 religions' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_religion_updated_returned(): void
    {
        $religion = Religion::factory()->create();

        $species = Species::factory()->for($religion->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($religion->compendium->creator)
            ->putJson("/api/religions/$religion->slug?include=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $religion->compendium->id,
                    'name' => $religion->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('religions', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $religion->refresh();

        $this->assertTrue($religion->compendium->creator->can('update', $religion));
        $this->assertTrue($religion->compendium->creator->can('delete', $religion));
    }
}
