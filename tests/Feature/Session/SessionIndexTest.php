<?php

namespace Tests\Feature\Session;

use App\Http\Resources\CampaignResource;
use App\Models\Session;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SessionIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $response = $this->getJson('/api/sessions');

        $response->assertUnauthorized();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_see(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/sessions');

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_sessions_returned(): void
    {
        $user = $this->userWithRole('sessions.view', 'admin');

        $sessions = Session::factory(10)->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/sessions');

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');

        $response->assertJson([
            'data' => $sessions->map(fn($session) => [
                'id' => $session->id,
                'slug' => $session->slug,
                'session_number' => $session->session_number,
                'title' => $session->title,
                'scheduled_at' => $session->scheduled_at->format('Y-m-d H:i:s'),
                'duration' => $session->duration,
                'location' => $session->location,
                'notes' => $session->notes,
            ])->toArray()
        ]);

    }
}
