<?php

namespace Tests\Feature\Campaign;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CampaignIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $response = $this->getJson('/api/campaigns');

        $response->assertUnauthorized();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_see(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/campaigns');

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_campaigns_returned(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('campaigns.view');

        $campaignsGameMaster = Campaign::factory(10)->for($user, 'gameMaster')->create();
        $campaignsCanSee = Campaign::factory(10)->create();
        foreach ($campaignsCanSee as $campaign) {
            Permission::create(['name' => 'campaigns.view.' . $campaign->id]);
            $user->givePermissionTo('campaigns.view.' . $campaign->id);
        }

        $campaignsCannotSee = Campaign::factory(10)->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/campaigns');

        $response->assertSuccessful();

        $response->assertJsonCount(20, 'data');

        $response->assertJson([
            'data' => $campaignsGameMaster->concat($campaignsCanSee)->map(fn($campaign) => [
                'id' => $campaign->id,
                'slug' => $campaign->slug,
                'name' => $campaign->name,
                'content' => $campaign->content,
                'start_date' => $campaign->start_date,
                'end_date' => $campaign->end_date,
                'level' => $campaign->level,
                'active' => $campaign->active,
                'visibility' => $campaign->visibility->value,
                'player_limit' => $campaign->player_limit
            ])->toArray()
        ]);

        $response->assertJsonMissing([
            'data' => $campaignsCannotSee->map(fn($campaign) => [
                'id' => $campaign->id,
                'slug' => $campaign->slug,
                'name' => $campaign->name,
                'content' => $campaign->content,
                'start_date' => $campaign->start_date,
                'end_date' => $campaign->end_date,
                'level' => $campaign->level,
                'active' => $campaign->active,
                'visibility' => $campaign->visibility->value,
                'player_limit' => $campaign->player_limit
            ])->toArray()
        ]);

    }
}
