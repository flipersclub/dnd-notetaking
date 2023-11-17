<?php

namespace Tests\Feature\Compendium\NaturalResource;

use App\Models\Compendium\NaturalResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NaturalResourceShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $naturalResource = NaturalResource::factory()->create();

        $response = $this->getJson("/api/natural-resources/$naturalResource->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_naturalResource_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/natural-resources/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_naturalResource(): void
    {
        $user = User::factory()->create();

        $naturalResource = NaturalResource::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/natural-resources/$naturalResource->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_naturalResource(): void
    {
        $naturalResource = NaturalResource::factory()->create();

        $response = $this->actingAs($naturalResource->compendium->creator)
            ->getJson("/api/natural-resources/$naturalResource->slug?include=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $naturalResource->name,
                'content' => $naturalResource->content,
                'compendium' => [
                    'id' => $naturalResource->compendium->id,
                    'name' => $naturalResource->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_naturalResource(): void
    {
        $naturalResource = NaturalResource::factory()->create();

        $user = $this->userWithPermission("naturalResources.view.$naturalResource->id");

        $response = $this->actingAs($user)
            ->getJson("/api/natural-resources/$naturalResource->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_naturalResource(): void
    {
        $naturalResource = NaturalResource::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/natural-resources/$naturalResource->slug");

        $response->assertSuccessful();

    }
}
