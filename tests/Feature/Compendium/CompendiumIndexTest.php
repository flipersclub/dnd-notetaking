<?php

namespace Tests\Feature\Compendium;

use App\Models\Compendium\Compendium;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompendiumIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $response = $this->getJson('/api/compendia');

        $response->assertUnauthorized();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_see(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/compendia');

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_compendia_returned(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('compendia.view');

        $compendia = Compendium::factory(10)->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/compendia');

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');

        $response->assertJson([
            'data' => $compendia->map(fn($compendium) => [
                'id' => $compendium->id,
                'name' => $compendium->name,
                'content' => $compendium->content
            ])->toArray()
        ]);

    }
    public function test_it_returns_successful_if_compendia_returned_with_creator(): void
    {
        $compendia = Compendium::factory(10)->create();

        $response = $this->asAdmin()
                         ->getJson('/api/compendia?with=creator');

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');

        $response->assertJson([
            'data' => $compendia->map(fn($compendium) => [
                'id' => $compendium->id,
                'slug' => $compendium->slug,
                'name' => $compendium->name,
                'content' => $compendium->content,
                'creator' => [
                    'id' => $compendium->creator->id,
                    'name' => $compendium->creator->name,
                    'email' => $compendium->creator->email
                ]
            ])->toArray()
        ]);

    }
}
