<?php

namespace Tests\Feature\Compendium\Currency;

use App\Models\Compendium\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CurrencyShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $currency = Currency::factory()->create();

        $response = $this->getJson("/api/currencies/$currency->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_currency_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/currencies/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_currency(): void
    {
        $user = User::factory()->create();

        $currency = Currency::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/currencies/$currency->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_currency(): void
    {
        $currency = Currency::factory()->create();

        $response = $this->actingAs($currency->compendium->creator)
            ->getJson("/api/currencies/$currency->slug?with=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $currency->name,
                'content' => $currency->content,
                'compendium' => [
                    'id' => $currency->compendium->id,
                    'name' => $currency->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_currency(): void
    {
        $currency = Currency::factory()->create();

        $user = $this->userWithPermission("currencies.view.$currency->id");

        $response = $this->actingAs($user)
            ->getJson("/api/currencies/$currency->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_currency(): void
    {
        $currency = Currency::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/currencies/$currency->slug");

        $response->assertSuccessful();

    }
}
