<?php

namespace Tests\Feature\Compendium\Concept;

use App\Models\Compendium\Concept;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConceptShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $concept = Concept::factory()->create();

        $response = $this->getJson("/api/concepts/$concept->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_concept_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/concepts/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_concept(): void
    {
        $user = User::factory()->create();

        $concept = Concept::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/concepts/$concept->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_concept(): void
    {
        $concept = Concept::factory()->create();

        $response = $this->actingAs($concept->compendium->creator)
            ->getJson("/api/concepts/$concept->slug?with=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $concept->name,
                'content' => $concept->content,
                'compendium' => [
                    'id' => $concept->compendium->id,
                    'name' => $concept->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_concept(): void
    {
        $concept = Concept::factory()->create();

        $user = $this->userWithPermission("concepts.view.$concept->id");

        $response = $this->actingAs($user)
            ->getJson("/api/concepts/$concept->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_concept(): void
    {
        $concept = Concept::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/concepts/$concept->slug");

        $response->assertSuccessful();

    }
}
