<?php

namespace Feature\System;

use App\Models\System;
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

class SystemShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $system = System::factory()->create();

        $response = $this->getJson("/api/systems/$system->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_system_not_existent(): void
    {
        $user = User::factory()->create();

        $system = System::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/systems/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_update_system(): void
    {
        $user = User::factory()->create();

        $system = System::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/systems/$system->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_system_returned(): void
    {
        $system = System::factory()->create();

        $user = $this->userWithPermission("systems.view.$system->id");

        $response = $this->actingAs($user)
            ->getJson("/api/systems/$system->slug");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'slug' => $system->slug,
                'name' => $system->name,
                'content' => $system->content
            ]
        ]);

    }
}
