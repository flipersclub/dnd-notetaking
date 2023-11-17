<?php

namespace Tests\Feature\Campaign;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CampaignShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $campaign = Campaign::factory()->create();

        $response = $this->getJson("/api/campaigns/$campaign->id");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_campaign_not_existent(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/campaigns/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_update_campaign(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/campaigns/$campaign->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_campaign_returned(): void
    {
        $campaign = Campaign::factory()->forGameMaster()->create();

        $user = $this->userWithPermission("campaigns.view.$campaign->id");

        $response = $this->actingAs($user)
            ->getJson("/api/campaigns/$campaign->slug?include=gameMaster");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'id' => $campaign->id,
                'slug' => $campaign->slug,
                'name' => $campaign->name,
                'content' => $campaign->content,
                'start_date' => $campaign->start_date,
                'end_date' => $campaign->end_date,
                'level' => $campaign->level,
                'active' => $campaign->active,
                'visibility' => $campaign->visibility->value,
                'player_limit' => $campaign->player_limit,
                'gameMaster' => [
                    'id' => $campaign->gameMaster->id,
                    'name' => $campaign->gameMaster->name,
                    'email' => $campaign->gameMaster->email,
                ],
            ]
        ]);

    }
}
