<?php

namespace Tests\Feature\Setting;

use App\Models\System;
use App\Models\User;
use Faker\Provider\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettingCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $response = $this->postJson('/api/settings');

        $response->assertUnauthorized();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_see(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->postJson('/api/settings');

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $user = $this->userWithRole('settings.create', 'admin');

        $response = $this->actingAs($user)
                         ->postJson('/api/settings', $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseEmpty('settings');

    }

    public static function validationDataProvider()
    {
        return [
            'name not present' => [[], ['name' => 'The name field is required.']],
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

    public function test_it_returns_successful_if_settings_returned(): void
    {
        $user = $this->userWithRole('settings.create', 'admin');

        $file = UploadedFile::fake()->image('avatar.jpg', 1020, 100);

        Storage::fake();
        Carbon::setTestNow(now());

        $response = $this->actingAs($user)
                         ->postJson('/api/settings?with=creator', [
                             'name' => 'D&D',
                             'description' => ($description = Str::random(65535)),
                             'cover_image' => $file
                         ]);

        $response->assertSuccessful();

        Storage::assertExists('settings/' . $file->hashName());

        $response->assertJson([
            'data' => [
                'name' => 'D&D',
                'description' => $description,
                'cover_image' => env('APP_URL') . '/settings/' . $file->hashName() . '?expiration=' . now()->addMinutes(5)->timestamp,
                'creator' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]
        ]);

        $this->assertDatabaseHas('settings', [
            'name' => 'D&D',
            'creator_id' => $user->getKey(),
            'description' => $description,
            'cover_image' => 'settings/' . $file->hashName()
        ]);

    }
}