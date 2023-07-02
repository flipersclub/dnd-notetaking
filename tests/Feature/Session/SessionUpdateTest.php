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

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $session = Session::factory()->create();

        $response = $this->putJson("/api/sessions/$session->id");

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
            ->putJson("/api/sessions/$session->id");

        $response->assertForbidden();
    }

    /** @dataProvider updateValidationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $session = Session::factory()->create();

        $user = $this->userWithRole('sessions.update', 'admin');

        $response = $this->actingAs($user)
            ->putJson('/api/sessions/' . $session->id, $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);
    }

    public static function updateValidationDataProvider()
    {
        return [
            'session_number not an integer' => [['session_number' => 'invalid-number'], ['session_number' => 'The session number field must be an integer.']],
            'title not a string' => [['title' => ['an', 'array']], ['title' => 'The title field must be a string.']],
            'title longer than 255 characters' => [['title' => Str::random(256)], ['title' => 'The title field must not be greater than 255 characters.']],
            'scheduled_at not a valid date' => [['scheduled_at' => 'invalid-date'], ['scheduled_at' => 'The scheduled at field must be a valid date.']],
            'duration not an integer' => [['duration' => 'not-an-integer'], ['duration' => 'The duration field must be an integer.']],
            'duration less than 0' => [['duration' => -1], ['duration' => 'The duration field must be at least 0.']],
            'location not a string' => [['location' => ['an', 'array']], ['location' => 'The location field must be a string.']],
            'location longer than 255 characters' => [['location' => Str::random(256)], ['location' => 'The location field must not be greater than 255 characters.']],
            'notes not a string' => [['notes' => ['an', 'array']], ['notes' => 'The notes field must be a string.']],
            'cover_image not an image file' => [['cover_image' => UploadedFile::fake()->create('document.pdf')], ['cover_image' => 'The cover image field must be an image.']],
            'cover_image larger than 2MB' => [['cover_image' => UploadedFile::fake()->image('avatar.jpg')->size(3000)], ['cover_image' => 'The cover image field must not be greater than 2048 kilobytes.']],
        ];
    }
    public function test_it_returns_successful_if_session_updated_returned(): void
    {
        $user = User::factory()->create();
        $session = Session::factory()->forCampaign(['game_master_id' => $user->id])->create();

        $file = UploadedFile::fake()->image('avatar.jpg', 1020, 100);

        Storage::fake();
        Carbon::setTestNow(now());

        $payload = [
            'session_number' => 2,
            'title' => 'Updated Session Title',
            'scheduled_at' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'duration' => 120,
            'location' => 'Updated Location',
            'notes' => 'Updated notes',
            'cover_image' => $file,
        ];

        $response = $this->actingAs($user)
            ->putJson('/api/sessions/' . $session->id, $payload);

        $response->assertSuccessful();

        Storage::assertExists('sessions/' . $file->hashName());

        $response->assertJson([
            'data' => [
                'session_number' => $payload['session_number'],
                'title' => $payload['title'],
                'scheduled_at' => $payload['scheduled_at'],
                'duration' => $payload['duration'],
                'location' => $payload['location'],
                'notes' => $payload['notes'],
                'cover_image' => env('APP_URL') . '/sessions/' . $file->hashName() . '?expiration=' . now()->addMinutes(5)->timestamp,
            ],
        ]);

        $this->assertDatabaseHas('sessions', [
            'id' => $session->id,
            'session_number' => $payload['session_number'],
            'title' => $payload['title'],
            'scheduled_at' => $payload['scheduled_at'],
            'duration' => $payload['duration'],
            'location' => $payload['location'],
            'notes' => $payload['notes'],
            'cover_image' => 'sessions/' . $file->hashName()
        ]);
    }

}
