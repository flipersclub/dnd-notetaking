<?php

namespace Tests\Feature\Compendium\NaturalResource;

use App\Models\Compendium\NaturalResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NaturalResourceDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $naturalResource = NaturalResource::factory()->create();

        $response = $this->deleteJson("/api/natural-resources/$naturalResource->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_naturalResource_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/natural-resources/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_naturalResource(): void
    {
        $user = User::factory()->create();

        $naturalResource = NaturalResource::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/natural-resources/$naturalResource->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_naturalResource_deleted(): void
    {
        $naturalResource = NaturalResource::factory()->create();

        $response = $this->actingAs($naturalResource->compendium->creator)
            ->deleteJson("/api/natural-resources/$naturalResource->slug");

        $response->assertNoContent();

        $this->assertModelMissing($naturalResource);

    }
}
