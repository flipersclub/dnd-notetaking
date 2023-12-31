<?php

namespace Tests\Feature\Session;

use App\Models\Campaign;
use App\Models\Session;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class SessionUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $session = Session::factory()->create();

        $response = $this->putJson("/api/sessions/$session->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_session_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/sessions/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_update_session(): void
    {
        $user = User::factory()->create();

        $session = Session::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/sessions/$session->slug");

        $response->assertForbidden();
    }

    /** @dataProvider updateValidationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $session = Session::factory()->create();

        $response = $this->actingAs($session->campaign->gameMaster)
            ->putJson('/api/sessions/' . $session->slug, $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);
    }

    public static function updateValidationDataProvider()
    {
        return [
            'session_number not an integer' => [['session_number' => 'invalid-number'], ['session_number' => 'The session number field must be an integer.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 characters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'scheduled_at not a valid date' => [['scheduled_at' => 'invalid-date'], ['scheduled_at' => 'The scheduled at field must be a valid date.']],
            'duration not an integer' => [['duration' => 'not-an-integer'], ['duration' => 'The duration field must be an integer.']],
            'duration less than 0' => [['duration' => -1], ['duration' => 'The duration field must be at least 0.']],
            'location not a string' => [['location' => ['an', 'array']], ['location' => 'The location field must be a string.']],
            'location longer than 255 characters' => [['location' => Str::random(256)], ['location' => 'The location field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
        ];
    }
    public function test_it_returns_successful_if_session_updated_returned(): void
    {
        $session = Session::factory()->create();

        $payload = [
            'session_number' => 2,
            'name' => 'Updated Session Title',
            'scheduled_at' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'duration' => 120,
            'location' => 'Updated Location',
            'content' => 'Updated content',
        ];

        $response = $this->actingAs($session->campaign->gameMaster)
            ->putJson('/api/sessions/' . $session->slug, $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'session_number' => $payload['session_number'],
                'name' => $payload['name'],
                'scheduled_at' => $payload['scheduled_at'],
                'duration' => $payload['duration'],
                'location' => $payload['location'],
                'content' => $payload['content'],
            ],
        ]);

        $this->assertDatabaseHas('sessions', [
            'id' => $session->id,
            'session_number' => $payload['session_number'],
            'name' => $payload['name'],
            'scheduled_at' => $payload['scheduled_at'],
            'duration' => $payload['duration'],
            'location' => $payload['location'],
            'content' => $payload['content'],
        ]);
    }

}
