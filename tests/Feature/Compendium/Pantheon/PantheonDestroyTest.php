<?php

namespace Tests\Feature\Compendium\Pantheon;

use App\Models\Compendium\Pantheon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PantheonDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $pantheon = Pantheon::factory()->create();

        $response = $this->deleteJson("/api/pantheons/$pantheon->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_pantheon_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/pantheons/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_pantheon(): void
    {
        $user = User::factory()->create();

        $pantheon = Pantheon::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/pantheons/$pantheon->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_pantheon_deleted(): void
    {
        $pantheon = Pantheon::factory()->create();

        $response = $this->actingAs($pantheon->compendium->creator)
            ->deleteJson("/api/pantheons/$pantheon->slug");

        $response->assertNoContent();

        $this->assertModelMissing($pantheon);

    }
}
