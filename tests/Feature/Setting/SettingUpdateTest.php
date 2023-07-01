<?php

namespace Tests\Feature\Setting;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettingUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $setting = Setting::factory()->create();

        $response = $this->putJson("/api/settings/$setting->id");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_setting_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/settings/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_update_setting(): void
    {
        $user = User::factory()->create();

        $setting = Setting::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/settings/$setting->id");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $setting = Setting::factory()->create();

        $user = $this->userWithRole("settings.update.$setting->id", 'admin');

        $response = $this->actingAs($user)
            ->putJson("/api/settings/$setting->id", $payload);

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
            'description not a string' => [['description' => ['an', 'array']], ['description' => 'The description field must be a string.']],
            'description longer than 255 characters' => [['description' => Str::random(65536)], ['description' => 'The description field must not be greater than 65535 characters.']],
            'cover_image not a string' => [['cover_image' => ['an', 'array']], ['cover_image' => 'The cover image field must be an image.']],
            'cover_image less than 1020px h' => [['cover_image' => UploadedFile::fake()->image('avatar.jpg', 100, 100)], ['cover_image' => 'The cover image field has invalid image dimensions.']],
            'cover_image less than 100px h' => [['cover_image' => UploadedFile::fake()->image('avatar.jpg', 1100, 20)], ['cover_image' => 'The cover image field has invalid image dimensions.']],
        ];
    }

    public function test_it_returns_successful_if_setting_updated_returned(): void
    {
        $setting = Setting::factory()->create();

        $user = $this->userWithPermission("settings.update.$setting->id");
        $newUser = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg', 1020, 100);

        Storage::fake();
        Carbon::setTestNow(now());

        $response = $this->actingAs($user)
            ->putJson("/api/settings/$setting->id?with=creator", [
                'name' => 'D&D',
                'description' => ($description = Str::random(65535)),
                'cover_image' => $file,
                'creator_id' => $newUser->getKey()
            ]);

        $response->assertSuccessful();

        Storage::assertExists('settings/' . $file->hashName());

        $response->assertJson([
            'data' => [
                'name' => 'D&D',
                'description' => $description,
                'cover_image' => env('APP_URL') . '/settings/' . $file->hashName() . '?expiration=' . Carbon::getTestNow()->addMinutes(5)->timestamp,
                'creator' => [
                    'id' => $newUser->id,
                    'name' => $newUser->name,
                    'email' => $newUser->email
                ]
            ]
        ]);

        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'name' => 'D&D',
            'creator_id' => $newUser->getKey(),
            'description' => $description,
            'cover_image' => 'settings/' . $file->hashName()
        ]);

    }
}
