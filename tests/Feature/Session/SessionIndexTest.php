<?php

namespace Tests\Feature\Session;

use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
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

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $campaign = Campaign::factory()->create();
        $response = $this->getJson("/api/campaigns/$campaign->slug/sessions");

        $response->assertUnauthorized();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see(): void
    {
        $campaign = Campaign::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson("/api/campaigns/$campaign->slug/sessions");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_sessions_returned(): void
    {
        $campaign = Campaign::factory()->create();
        $sessions = Session::factory(10)->for($campaign)->create();
        $otherSessions = Session::factory(10)->create();

        $response = $this->asAdmin()
                         ->getJson("/api/campaigns/$campaign->slug/sessions?with=campaign");

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');

        $response->assertJson([
            'data' => $sessions->map(fn($session) => [
                'id' => $session->id,
                'slug' => $session->slug,
                'session_number' => $session->session_number,
                'name' => $session->name,
                'scheduled_at' => $session->scheduled_at->format('Y-m-d H:i:s'),
                'duration' => $session->duration,
                'location' => $session->location,
            ])->toArray()
        ]);

        $response->assertJsonMissing([
            'data' => $otherSessions->map(fn($session) => [
                'id' => $session->id,
                'session_number' => $session->session_number,
                'name' => $session->name,
                'scheduled_at' => $session->scheduled_at->format('Y-m-d H:i:s'),
                'duration' => $session->duration,
                'location' => $session->location,
            ])->toArray()
        ]);

    }
}
