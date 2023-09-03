<?php

namespace Tests\Feature\Compendium\Species;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpeciesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $compendium = Compendium::factory()->create();
        $response = $this->getJson("/api/compendia/$compendium->slug/species");

        $response->assertUnauthorized();
    }

    public function test_it_returns_forbidden_if_user_hasnt_created_a_compendium(): void
    {
        $compendium = Compendium::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/compendia/$compendium->slug/species");

        $response->assertForbidden();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_for_this_compendium(): void
    {
        $compendium = Compendium::factory()->create();
        $user = User::factory()->hasCompendia(3)->create();

        $response = $this->actingAs($user)
            ->getJson("/api/compendia/$compendium->slug/species");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_species_returned(): void
    {
        $compendium = Compendium::factory()->create();

        $dontSeeSpecies = Species::factory(10)->create();
        $seeSpecies = Species::factory(10)
            ->for($compendium)
            ->create();

        $response = $this->actingAs($compendium->creator)
            ->getJson("/api/compendia/$compendium->slug/species?with=compendium");

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');

        $response->assertJson([
            'data' => $seeSpecies->map(fn(Species $species) => [
                'name' => $species->name,
                'content' => $species->content,
                'compendium' => [
                    'id' => $compendium->id,
                    'name' => $compendium->name
                ]
            ])->toArray()
        ]);
    }

    public function test_admin_can_see_other_user_compendiums(): void
    {
        $compendium = Compendium::factory()->create();

        $dontSeeSpecies = Species::factory(10)->create();
        $seeSpecies = Species::factory(10)->for($compendium)->create();

        $response = $this->asAdmin()
            ->getJson("/api/compendia/$compendium->slug/species");

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');
    }
}
