<?php

namespace Tests\Feature\Compendium\Calendar;

use App\Models\Compendium\Calendar\Calendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CalendarShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $calendar = Calendar::factory()->create();

        $response = $this->getJson("/api/calendars/$calendar->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_calendar_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/calendars/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_calendar(): void
    {
        $user = User::factory()->create();

        $calendar = Calendar::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/calendars/$calendar->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_calendar(): void
    {
        $calendar = Calendar::factory()->create();

        $response = $this->actingAs($calendar->compendium->creator)
            ->getJson("/api/calendars/$calendar->slug?include=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $calendar->name,
                'content' => $calendar->content,
                'compendium' => [
                    'id' => $calendar->compendium->id,
                    'name' => $calendar->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_calendar(): void
    {
        $calendar = Calendar::factory()->create();

        $user = $this->userWithPermission("calendars.view.$calendar->id");

        $response = $this->actingAs($user)
            ->getJson("/api/calendars/$calendar->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_calendar(): void
    {
        $calendar = Calendar::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/calendars/$calendar->slug");

        $response->assertSuccessful();

    }
}
