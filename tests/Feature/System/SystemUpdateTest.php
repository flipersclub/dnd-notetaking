<?php

namespace Tests\Feature\System;

use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SystemUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $system = System::factory()->create();

        $response = $this->putJson("/api/systems/$system->id");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_system_not_existent(): void
    {
        $user = User::factory()->create();

        $system = System::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/systems/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_update_system(): void
    {
        $user = User::factory()->create();

        $system = System::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/systems/$system->id");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $system = System::factory()->create();

        $permission = Permission::create(['name' => "systems.update.$system->id"]);
        $user = User::factory()->create();
        $user->givePermissionTo($permission);

        $response = $this->actingAs($user)
            ->putJson("/api/systems/$system->id", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 characters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'description not a string' => [['description' => ['an', 'array']], ['description' => 'The description field must be a string.']],
            'description longer than 255 characters' => [['description' => Str::random(65536)], ['description' => 'The description field must not be greater than 65535 characters.']],
            'cover_image not a string' => [['cover_image' => ['an', 'array']], ['cover_image' => 'The cover image field must be an image.']],
            'cover_image less than 1020px h' => [['cover_image' => UploadedFile::fake()->image('avatar.jpg', 100, 100)], ['cover_image' => 'The cover image field has invalid image dimensions.']],
            'cover_image less than 100px h' => [['cover_image' => UploadedFile::fake()->image('avatar.jpg', 1100, 20)], ['cover_image' => 'The cover image field has invalid image dimensions.']],
        ];
    }

    public function test_it_returns_successful_if_system_updated_returned(): void
    {
        $system = System::factory()->create();

        $permission = Permission::create(['name' => "systems.update.$system->id"]);
        $user = User::factory()->create();
        $user->givePermissionTo($permission);

        $file = UploadedFile::fake()->image('avatar.jpg', 1020, 100);

        Storage::fake();

        $response = $this->actingAs($user)
            ->putJson("/api/systems/$system->id", [
                'name' => 'D&D',
                'description' => ($description = Str::random(65535)),
                'cover_image' => $file
            ]);

        $response->assertSuccessful();

        Storage::assertExists('systems/' . $file->hashName());

        $response->assertJson([
            'data' => [
                'name' => 'D&D',
                'description' => $description,
                'cover_image' => 'systems/' . $file->hashName()
            ]
        ]);

        $this->assertDatabaseHas('systems', [
            'id' => $system->id,
            'name' => 'D&D',
            'description' => $description,
            'cover_image' => 'systems/' . $file->hashName()
        ]);

    }
}
