<?php

namespace Tests\Feature\Compendium\Location;

use App\Enums\Compendium\Location\LocationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTypeIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $response = $this->getJson('/api/location-types');

        $response->assertUnauthorized();
    }

    public function test_it_returns_successful_if_campaigns_returned(): void
    {
        $response = $this->signedIn()
            ->getJson('/api/location-types');

        $response->assertSuccessful();

        $cases = LocationType::cases();

        $response->assertJsonCount(count($cases), 'data');

        $response->assertJson([
            'data' => collect($cases)->map(fn(LocationType $case) => [
                'id' => $case->value,
                'name' => $case->label(),
            ])->toArray()
        ]);

    }
}
