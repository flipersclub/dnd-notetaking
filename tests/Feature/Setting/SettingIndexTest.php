<?php

namespace Tests\Feature\Setting;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettingIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $response = $this->getJson('/api/settings');

        $response->assertUnauthorized();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_see(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/settings');

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_settings_returned(): void
    {
        $user = $this->userWithRole('settings.view', 'admin');

        $settings = Setting::factory(10)->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/settings');

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');

        $response->assertJson([
            'data' => $settings->map(fn($setting) => [
                'id' => $setting->id,
                'name' => $setting->name,
                'description' => $setting->description
            ])->toArray()
        ]);

    }
    public function test_it_returns_successful_if_settings_returned_with_creator(): void
    {
        $user = $this->userWithRole('settings.view', 'admin');

        $settings = Setting::factory(10)->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/settings?with=creator');

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');

        $response->assertJson([
            'data' => $settings->map(fn($setting) => [
                'id' => $setting->id,
                'slug' => $setting->slug,
                'name' => $setting->name,
                'description' => $setting->description,
                'creator' => [
                    'id' => $setting->creator->id,
                    'name' => $setting->creator->name,
                    'email' => $setting->creator->email
                ]
            ])->toArray()
        ]);

    }
}
