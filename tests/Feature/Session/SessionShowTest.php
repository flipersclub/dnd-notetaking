<?php

namespace Tests\Feature\Session;

use App\Models\Session;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SessionShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $session = Session::factory()->create();

        $response = $this->getJson("/api/sessions/$session->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_session_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/sessions/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_update_session(): void
    {
        $user = User::factory()->create();

        $session = Session::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/sessions/$session->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_session_returned(): void
    {
        $session = Session::factory()->create();

        $user = $this->userWithPermission("sessions.view.$session->id");

        $response = $this->actingAs($user)
            ->getJson("/api/sessions/$session->slug?with=campaign");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'id' => $session->id,
                'slug' => $session->slug,
                'session_number' => $session->session_number,
                'title' => $session->title,
                'scheduled_at' => $session->scheduled_at->format('Y-m-d H:i:s'),
                'duration' => $session->duration,
                'location' => $session->location,
                'notes' => $session->notes,
                'campaign' => [
                    'id' => $session->campaign->id,
                    'slug' => $session->campaign->slug,
                    'name' => $session->campaign->name,
                    'description' => $session->campaign->description,
                    'start_date' => $session->campaign->start_date,
                    'end_date' => $session->campaign->end_date,
                    'level' => $session->campaign->level,
                    'active' => $session->campaign->active,
                    'visibility' => $session->campaign->visibility->value,
                    'player_limit' => $session->campaign->player_limit,
                ],
            ]
        ]);

    }

    public function test_it_returns_successful_if_user_is_game_master(): void
    {
        $user = User::factory()->create();

        $session = Session::factory()->forCampaign(['game_master_id' => $user->id])->create();

        $response = $this->actingAs($user)
            ->getJson("/api/sessions/$session->slug?with=campaign");

        $response->assertSuccessful();
    }
}
