<?php

namespace Tests\Feature\Setting;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettingDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $setting = Setting::factory()->create();

        $response = $this->deleteJson("/api/settings/$setting->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_setting_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/settings/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_delete_setting(): void
    {
        $user = User::factory()->create();

        $setting = Setting::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/settings/$setting->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_setting_deleted(): void
    {
        $setting = Setting::factory()->create();

        $user = $this->userWithPermission("settings.delete.$setting->id");

        $response = $this->actingAs($user)
            ->deleteJson("/api/settings/$setting->slug");

        $response->assertNoContent();

        $this->assertModelMissing($setting);

    }
}
