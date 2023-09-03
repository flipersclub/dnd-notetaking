<?php

namespace Tests\Feature\Compendium\Deity;

use App\Models\Compendium\Deity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeityDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $deity = Deity::factory()->create();

        $response = $this->deleteJson("/api/deities/$deity->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_deity_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/deities/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_deity(): void
    {
        $user = User::factory()->create();

        $deity = Deity::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/deities/$deity->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_deity_deleted(): void
    {
        $deity = Deity::factory()->create();

        $response = $this->actingAs($deity->compendium->creator)
            ->deleteJson("/api/deities/$deity->slug");

        $response->assertNoContent();

        $this->assertModelMissing($deity);

    }
}
