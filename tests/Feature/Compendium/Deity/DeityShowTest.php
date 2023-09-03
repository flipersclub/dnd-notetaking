<?php

namespace Tests\Feature\Compendium\Deity;

use App\Models\Compendium\Deity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeityShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $deity = Deity::factory()->create();

        $response = $this->getJson("/api/deities/$deity->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_deity_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/deities/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_deity(): void
    {
        $user = User::factory()->create();

        $deity = deity::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/deities/$deity->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_deity(): void
    {
        $deity = Deity::factory()->create();

        $response = $this->actingAs($deity->compendium->creator)
            ->getJson("/api/deities/$deity->slug?with=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $deity->name,
                'content' => $deity->content,
                'compendium' => [
                    'id' => $deity->compendium->id,
                    'name' => $deity->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_deity(): void
    {
        $deity = Deity::factory()->create();

        $user = $this->userWithPermission("deities.view.$deity->id");

        $response = $this->actingAs($user)
            ->getJson("/api/deities/$deity->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_deity(): void
    {
        $deity = Deity::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/deities/$deity->slug");

        $response->assertSuccessful();

    }
}
