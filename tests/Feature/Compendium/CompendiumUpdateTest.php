<?php

namespace Tests\Feature\Compendium;

use App\Models\Compendium\Compendium;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CompendiumUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $compendium = Compendium::factory()->create();

        $response = $this->putJson("/api/compendia/$compendium->id");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_compendium_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/compendia/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_update_compendium(): void
    {
        $user = User::factory()->create();

        $compendium = Compendium::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/compendia/$compendium->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $compendium = Compendium::factory()->create();

        $user = $this->userWithRole("compendia.update.$compendium->id", 'admin');

        $response = $this->actingAs($user)
            ->putJson("/api/compendia/$compendium->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 characters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'creator_id empty' => [['creator_id' => null], ['creator_id' => 'The creator id field is required.']],
            'creator_id invalid' => [['creator_id' => 99999], ['creator_id' => 'The selected creator id is invalid.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 255 characters' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.']],
        ];
    }

    public function test_it_returns_successful_if_compendium_updated_returned(): void
    {
        $compendium = Compendium::factory()->create();

        $user = $this->userWithPermission("compendia.update.$compendium->id");
        $newUser = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/compendia/$compendium->slug?with=creator", [
                'name' => 'D&D',
                'content' => ($content = Str::random(65535)),
                'creator_id' => $newUser->getKey()
            ]);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => 'D&D',
                'content' => $content,
                'creator' => [
                    'id' => $newUser->id,
                    'name' => $newUser->name,
                    'email' => $newUser->email
                ]
            ]
        ]);

        $this->assertDatabaseHas('compendia', [
            'id' => $compendium->id,
            'name' => 'D&D',
            'creator_id' => $newUser->getKey(),
            'content' => $content,
        ]);

    }
}
