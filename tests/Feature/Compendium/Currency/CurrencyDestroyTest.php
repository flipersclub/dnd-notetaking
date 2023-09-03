<?php

namespace Tests\Feature\Compendium\Currency;

use App\Models\Compendium\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CurrencyDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $currency = Currency::factory()->create();

        $response = $this->deleteJson("/api/currencies/$currency->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_currency_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/currencies/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_currency(): void
    {
        $user = User::factory()->create();

        $currency = Currency::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/currencies/$currency->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_currency_deleted(): void
    {
        $currency = Currency::factory()->create();

        $response = $this->actingAs($currency->compendium->creator)
            ->deleteJson("/api/currencies/$currency->slug");

        $response->assertNoContent();

        $this->assertModelMissing($currency);

    }
}
