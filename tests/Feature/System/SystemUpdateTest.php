<?php

namespace Tests\Feature\System;

use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class SystemUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $system = System::factory()->create();

        $response = $this->putJson("/api/systems/$system->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_system_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/systems/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_update_system(): void
    {
        $user = User::factory()->create();

        $system = System::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/systems/$system->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $system = System::factory()->create();

        $user = $this->userWithPermission("systems.update.$system->id");

        $response = $this->actingAs($user)
            ->putJson("/api/systems/$system->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 characters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 255 characters' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.']],
        ];
    }

    public function test_it_returns_successful_if_system_updated_returned(): void
    {
        $system = System::factory()->create();

        $user = $this->userWithPermission("systems.update.$system->id");

        $response = $this->actingAs($user)
            ->putJson("/api/systems/$system->slug", [
                'name' => 'D&D',
                'content' => ($content = Str::random(65535)),
            ]);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => 'D&D',
                'content' => $content,
            ]
        ]);

        $this->assertDatabaseHas('systems', [
            'slug' => $system->slug,
            'name' => 'D&D',
            'content' => $content
        ]);
    }
}
