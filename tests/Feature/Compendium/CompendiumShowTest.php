<?php

namespace Tests\Feature\Compendium;

use App\Models\Compendium\Compendium;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompendiumShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $compendium = Compendium::factory()->create();

        $response = $this->getJson("/api/compendia/$compendium->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_compendium_not_existent(): void
    {
        $user = User::factory()->create();

        $compendium = Compendium::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/compendia/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_update_compendium(): void
    {
        $user = User::factory()->create();

        $compendium = Compendium::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/compendia/$compendium->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_compendium_returned(): void
    {
        $compendium = Compendium::factory()->create();

        $response = $this->actingAs($compendium->creator)
            ->getJson("/api/compendia/$compendium->slug");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'id' => $compendium->id,
                'slug' => $compendium->slug,
                'name' => $compendium->name,
                'content' => $compendium->content
            ]
        ]);

    }

    public function test_it_returns_successful_if_compendium_returned_with_creator(): void
    {
        $compendium = Compendium::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/compendia/$compendium->slug?with=creator");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'id' => $compendium->id,
                'slug' => $compendium->slug,
                'name' => $compendium->name,
                'content' => $compendium->content,
                'creator' => [
                    'id' => $compendium->creator->id,
                    'name' => $compendium->creator->name,
                    'email' => $compendium->creator->email
                ]
            ]
        ]);

    }
}
