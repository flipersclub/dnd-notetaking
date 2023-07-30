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

class SettingShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $setting = Setting::factory()->create();

        $response = $this->getJson("/api/settings/$setting->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_setting_not_existent(): void
    {
        $user = User::factory()->create();

        $setting = Setting::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/settings/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_update_setting(): void
    {
        $user = User::factory()->create();

        $setting = Setting::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/settings/$setting->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_setting_returned(): void
    {
        $setting = Setting::factory()->create();

        $user = $this->userWithPermission("settings.view.$setting->id");

        $response = $this->actingAs($user)
            ->getJson("/api/settings/$setting->slug");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'id' => $setting->id,
                'slug' => $setting->slug,
                'name' => $setting->name,
                'content' => $setting->content
            ]
        ]);
        $response->assertJsonMissing([
            'id' => $user->id
        ]);

    }

    public function test_it_returns_successful_if_setting_returned_with_creator(): void
    {
        $setting = Setting::factory()->hasCreator()->create();

        $user = $this->userWithPermission("settings.view.$setting->id");

        $response = $this->actingAs($user)
            ->getJson("/api/settings/$setting->slug?with=creator");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'id' => $setting->id,
                'slug' => $setting->slug,
                'name' => $setting->name,
                'content' => $setting->content,
                'creator' => [
                    'id' => $setting->creator->id,
                    'name' => $setting->creator->name,
                    'email' => $setting->creator->email
                ]
            ]
        ]);

    }
}
