<?php

namespace Tests\Feature\Compendium\Currency;

use App\Models\Compendium\Currency;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CurrencyUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $currency = Currency::factory()->create();

        $response = $this->putJson("/api/currencies/$currency->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_currency_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/currencies/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_currency(): void
    {
        $user = User::factory()->create();

        $currency = Currency::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/currencies/$currency->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $currency = Currency::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/currencies/$currency->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('currencies', [
            'id' => $currency->id,
            // Ensure the original data is not modified
            'name' => $currency->name,
            'content' => $currency->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 currencies' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 currencies' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_currency_updated_returned(): void
    {
        $currency = Currency::factory()->create();

        $species = Species::factory()->for($currency->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($currency->compendium->creator)
            ->putJson("/api/currencies/$currency->slug?with=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $currency->compendium->id,
                    'name' => $currency->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('currencies', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $currency->refresh();

        $this->assertTrue($currency->compendium->creator->can('update', $currency));
        $this->assertTrue($currency->compendium->creator->can('delete', $currency));
    }
}
