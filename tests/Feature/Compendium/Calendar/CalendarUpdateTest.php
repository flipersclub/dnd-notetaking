<?php

namespace Tests\Feature\Compendium\Calendar;

use App\Models\Compendium\Calendar\Calendar;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CalendarUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $calendar = Calendar::factory()->create();

        $response = $this->putJson("/api/calendars/$calendar->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_calendar_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/calendars/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_calendar(): void
    {
        $user = User::factory()->create();

        $calendar = Calendar::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/calendars/$calendar->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $calendar = Calendar::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/calendars/$calendar->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('calendars', [
            'id' => $calendar->id,
            // Ensure the original data is not modified
            'name' => $calendar->name,
            'content' => $calendar->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 characters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 characters' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_calendar_updated_returned(): void
    {
        $calendar = Calendar::factory()->create();

        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($calendar->compendium->creator)
            ->putJson("/api/calendars/$calendar->slug?with=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $calendar->compendium->id,
                    'name' => $calendar->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('calendars', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $calendar->refresh();

        $this->assertTrue($calendar->compendium->creator->can('update', $calendar));
        $this->assertTrue($calendar->compendium->creator->can('delete', $calendar));
    }
}
