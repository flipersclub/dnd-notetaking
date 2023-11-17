<?php

namespace Tests\Feature\Compendium\Calendar;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Calendar\Calendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CalendarCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $compendium = Compendium::factory()->create();
        $response = $this->postJson("/api/compendia/$compendium->slug/calendars");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_compendium_does_not_exist(): void
    {
        $response = $this->signedIn()
            ->postJson("/api/compendia/lalalala/calendars");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_is_not_compendiums_creator(): void
    {
        $user = User::factory()->create();
        $compendium = Compendium::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/compendia/$compendium->slug/calendars");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $compendium = Compendium::factory()->create();

        $response = $this->asAdmin()
            ->postJson("/api/compendia/$compendium->slug/calendars", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseEmpty('calendars');

    }

    public static function validationDataProvider()
    {
        return [
            'name not present' => [[], ['name' => 'The name field is required.']],
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 characters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 characters' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.'],],
        ];
    }

    public function test_it_returns_successful_if_calendar_created(): void
    {
        $user = User::factory()->create();
        $compendium = Compendium::factory()->for($user, 'creator')->create();

        $payload = [
            'name' => 'John Doe',
            'age' => 30,
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($user)
            ->postJson("/api/compendia/$compendium->slug/calendars?include=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $compendium->id,
                    'name' => $compendium->name
                ]
            ],
        ]);

        $this->assertDatabaseHas('calendars', [
            'compendium_id' => $compendium->id,
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $calendar = Calendar::find($response->json('data')['id']);

        $user->refresh();

        $this->assertTrue($user->can('view', $calendar));
        $this->assertTrue($user->can('update', $calendar));
        $this->assertTrue($user->can('delete', $calendar));
    }

    public function test_it_returns_successful_if_admin_can_create(): void
    {
        $compendium = Compendium::factory()->create();

        $payload = [
            'name' => 'John Doe'
        ];

        $response = $this->asAdmin()
            ->postJson("/api/compendia/$compendium->slug/calendars", $payload);

        $response->assertSuccessful();

        $this->assertDatabaseHas('calendars', [
            'name' => $payload['name'],
            'compendium_id' => $compendium->id
        ]);
    }
}
