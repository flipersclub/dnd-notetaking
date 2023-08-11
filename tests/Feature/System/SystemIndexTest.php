<?php

namespace Tests\Feature\System;

use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SystemIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $response = $this->getJson('/api/systems');

        $response->assertUnauthorized();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_see(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/systems');

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_systems_returned(): void
    {
        $systems = System::factory(10)->create();

        $response = $this->asAdmin()
                         ->getJson('/api/systems');

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');

        $response->assertJson([
            'data' => $systems->map(fn($system) => [
                'id' => $system->id,
                'slug' => $system->slug,
                'name' => $system->name,
                'content' => $system->content
            ])->toArray()
        ]);

    }
}
