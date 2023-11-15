<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Compendium\Compendium;
use App\Models\Notebook;
use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MainIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $response = $this->getJson('/api/index');

        $response->assertUnauthorized();
    }

    public function test_it_gets_all_objects()
    {
        $user = User::factory()->create();

        System::factory(3)->create();
        Campaign::factory(3)->for($user, 'gameMaster')->create();
        Compendium::factory(3)->for($user, 'creator')
            ->hasLocations(3)
            ->hasCharacters(3)
            ->hasSpecies(3)
            ->hasItems(3)
            ->hasFactions(3)
            ->hasStories(3)
            ->hasConcepts(3)
            ->hasNaturalResources(3)
            ->hasCurrencies(3)
            ->hasLanguages(3)
            ->hasReligions(3)
            ->hasDeities(3)
            ->hasPantheons(3)
            ->hasPlanes(3)
            ->hasEncounters(3)
            ->hasQuests(3)
            ->hasSpells(3)
            ->create();
        Notebook::factory(3)->for($user)->hasNotes(3)->create();

        $response = $this->actingAs($user)
            ->get('/api/index');
        $response->assertSuccessful();

        $response->assertJsonCount(3, 'systems');
        $response->assertJsonCount(3, 'campaigns');
        $response->assertJsonCount(3, 'compendia');
        for ($i = 0; $i < 3; $i++) {
            $response->assertJsonCount(3, "compendia.$i.locations");
            $response->assertJsonCount(3, "compendia.$i.characters");
            $response->assertJsonCount(6, "compendia.$i.species");
            $response->assertJsonCount(3, "compendia.$i.items");
            $response->assertJsonCount(3, "compendia.$i.factions");
            $response->assertJsonCount(3, "compendia.$i.stories");
            $response->assertJsonCount(3, "compendia.$i.concepts");
            $response->assertJsonCount(3, "compendia.$i.natural_resources");
            $response->assertJsonCount(3, "compendia.$i.currencies");
            $response->assertJsonCount(3, "compendia.$i.languages");
            $response->assertJsonCount(3, "compendia.$i.religions");
            $response->assertJsonCount(3, "compendia.$i.deities");
            $response->assertJsonCount(3, "compendia.$i.pantheons");
            $response->assertJsonCount(3, "compendia.$i.planes");
            $response->assertJsonCount(3, "compendia.$i.encounters");
            $response->assertJsonCount(3, "compendia.$i.quests");
            $response->assertJsonCount(3, "compendia.$i.spells");
        }
        $response->assertJsonCount(3, 'notebooks');

        for ($i = 0; $i < 3; $i++) {
            $response->assertJsonCount(3, "notebooks.$i.notes");
        }


    }
}
