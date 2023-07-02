<?php

namespace Tests\Feature\Campaign;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CampaignDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $campaign = Campaign::factory()->create();

        $response = $this->deleteJson("/api/campaigns/$campaign->id");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_campaign_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/campaigns/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_delete_campaign(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/campaigns/$campaign->id");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_campaign_deleted(): void
    {
        $campaign = Campaign::factory()->create();

        $user = $this->userWithPermission("campaigns.delete.$campaign->id");

        $response = $this->actingAs($user)
            ->deleteJson("/api/campaigns/$campaign->id");

        $response->assertNoContent();

        $this->assertModelMissing($campaign);

    }
}
