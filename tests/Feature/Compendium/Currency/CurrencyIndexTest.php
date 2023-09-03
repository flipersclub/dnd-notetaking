<?php

namespace Tests\Feature\Compendium\Currency;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $compendium = Compendium::factory()->create();
        $response = $this->getJson("/api/compendia/$compendium->slug/currencies");

        $response->assertUnauthorized();
    }

    public function test_it_returns_forbidden_if_user_hasnt_created_a_compendium(): void
    {
        $compendium = Compendium::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/compendia/$compendium->slug/currencies");

        $response->assertForbidden();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_for_this_compendium(): void
    {
        $compendium = Compendium::factory()->create();
        $user = User::factory()->hasCompendia(3)->create();

        $response = $this->actingAs($user)
            ->getJson("/api/compendia/$compendium->slug/currencies");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_currencies_returned(): void
    {
        $compendium = Compendium::factory()->create();

        $dontSeeCurrencies = Currency::factory(10)->create();
        $seeCurrencies = Currency::factory(10)
            ->for($compendium)
            ->create();

        $response = $this->actingAs($compendium->creator)
            ->getJson("/api/compendia/$compendium->slug/currencies?with=compendium");

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');

        $response->assertJson([
            'data' => $seeCurrencies->map(fn(Currency $currency) => [
                'name' => $currency->name,
                'content' => $currency->content,
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

        $dontSeeCurrencies = Currency::factory(10)->create();
        $seeCurrencies = Currency::factory(10)->for($compendium)->create();

        $response = $this->asAdmin()
            ->getJson("/api/compendia/$compendium->slug/currencies");

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');
    }
}
