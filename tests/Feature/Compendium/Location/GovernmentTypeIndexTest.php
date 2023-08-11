<?php

namespace Tests\Feature\Compendium\Location;

use App\Enums\Compendium\Location\GovernmentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GovernmentTypeIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $response = $this->getJson('/api/government-types');

        $response->assertUnauthorized();
    }

    public function test_it_returns_successful_if_campaigns_returned(): void
    {
        $response = $this->signedIn()
            ->getJson('/api/government-types');

        $response->assertSuccessful();

        $cases = GovernmentType::cases();

        $response->assertJsonCount(count($cases), 'data');

        $response->assertJson([
            'data' => collect($cases)->map(fn(GovernmentType $case) => [
                'id' => $case->value,
                'name' => $case->label(),
            ])->toArray()
        ]);

    }
}
