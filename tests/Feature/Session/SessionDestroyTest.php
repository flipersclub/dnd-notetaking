<?php

namespace Tests\Feature\Session;

use App\Models\Session;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SessionDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $session = Session::factory()->create();

        $response = $this->deleteJson("/api/sessions/$session->id");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_session_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/sessions/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_delete_session(): void
    {
        $user = User::factory()->create();

        $session = Session::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/sessions/$session->id");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_session_deleted(): void
    {
        $session = Session::factory()->create();

        $user = $this->userWithPermission("sessions.delete.$session->id");

        $response = $this->actingAs($user)
            ->deleteJson("/api/sessions/$session->id");

        $response->assertNoContent();

        $this->assertModelMissing($session);

    }

    public function test_it_returns_successful_if_session_deleted_as_the_campaign_owner(): void
    {
        $user = User::factory()->create();

        $session = Session::factory()->forCampaign(['game_master_id' => $user->id])->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/sessions/$session->id");

        $response->assertNoContent();

        $this->assertModelMissing($session);

    }
}
