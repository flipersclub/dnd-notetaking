<?php

namespace Tests\Feature\Compendium\Pantheon;

use App\Models\Compendium\Pantheon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PantheonShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $pantheon = Pantheon::factory()->create();

        $response = $this->getJson("/api/pantheons/$pantheon->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_pantheon_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/pantheons/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_pantheon(): void
    {
        $user = User::factory()->create();

        $pantheon = Pantheon::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/pantheons/$pantheon->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_pantheon(): void
    {
        $pantheon = Pantheon::factory()->create();

        $response = $this->actingAs($pantheon->compendium->creator)
            ->getJson("/api/pantheons/$pantheon->slug?include=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $pantheon->name,
                'content' => $pantheon->content,
                'compendium' => [
                    'id' => $pantheon->compendium->id,
                    'name' => $pantheon->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_pantheon(): void
    {
        $pantheon = Pantheon::factory()->create();

        $user = $this->userWithPermission("pantheons.view.$pantheon->id");

        $response = $this->actingAs($user)
            ->getJson("/api/pantheons/$pantheon->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_pantheon(): void
    {
        $pantheon = Pantheon::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/pantheons/$pantheon->slug");

        $response->assertSuccessful();

    }
}
