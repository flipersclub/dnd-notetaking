<?php

namespace Tests\Feature\Compendium;

use App\Models\Compendium\Compendium;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompendiumDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $compendium = Compendium::factory()->create();

        $response = $this->deleteJson("/api/compendia/$compendium->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_compendium_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/compendia/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_delete_compendium(): void
    {
        $user = User::factory()->create();

        $compendium = Compendium::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/compendia/$compendium->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_compendium_deleted(): void
    {
        $compendium = Compendium::factory()->create();

        $response = $this->asAdmin()
            ->deleteJson("/api/compendia/$compendium->slug");

        $response->assertNoContent();

        $this->assertModelMissing($compendium);

    }

    public function test_it_is_successful_when_user_has_permission_to_view(): void
    {
        $compendium = Compendium::factory()->create();

        $user = User::factory()->create();

        $user->givePermissionTo("compendia.delete.{$compendium->id}");

        $response = $this->actingAs($user)
            ->deleteJson("/api/compendia/$compendium->slug");

        $response->assertNoContent();

    }

    public function test_it_is_successful_when_user_is_creator(): void
    {
        $compendium = Compendium::factory()->create();

        $response = $this->actingAs($compendium->creator)
            ->deleteJson("/api/compendia/$compendium->slug");

        $response->assertNoContent();

    }
}
