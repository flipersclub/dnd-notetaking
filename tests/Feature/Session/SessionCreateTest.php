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

class SessionCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $campaign = Campaign::factory()->create();
        $response = $this->postJson("/api/campaigns/{$campaign->slug}/sessions");

        $response->assertUnauthorized();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_see(): void
    {
        $campaign = Campaign::factory()->create();
        $user = User::factory()->hasCampaigns()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/campaigns/$campaign->slug/sessions");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $campaign = Campaign::factory()->create();

        $response = $this->asAdmin()
            ->postJson("/api/campaigns/$campaign->slug/sessions", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseEmpty('sessions');

    }

    public static function validationDataProvider()
    {
        return [
            'session_number not present' => [[], ['session_number' => 'The session number field is required.']],
            'session_number not an integer' => [['session_number' => 'invalid-number'], ['session_number' => 'The session number field must be an integer.']],
            'title not present' => [[], ['title' => 'The title field is required.']],
            'title not a string' => [['title' => ['an', 'array']], ['title' => 'The title field must be a string.']],
            'title longer than 255 characters' => [['title' => Str::random(256)], ['title' => 'The title field must not be greater than 255 characters.']],
            'scheduled_at not present' => [[], ['scheduled_at' => 'The scheduled at field is required.']],
            'scheduled_at not a valid date' => [['scheduled_at' => 'invalid-date'], ['scheduled_at' => 'The scheduled at field must be a valid date.']],
            'duration not an integer' => [['duration' => 'not-an-integer'], ['duration' => 'The duration field must be an integer.']],
            'duration less than 0' => [['duration' => -1], ['duration' => 'The duration field must be at least 0.']],
            'location not a string' => [['location' => ['an', 'array']], ['location' => 'The location field must be a string.']],
            'location longer than 255 characters' => [['location' => Str::random(256)], ['location' => 'The location field must not be greater than 255 characters.']],
            'notes not a string' => [['notes' => ['an', 'array']], ['notes' => 'The notes field must be a string.']],
        ];
    }

    public function test_it_returns_successful_if_session_created_returned(): void
    {
        $campaign = Campaign::factory()->forGameMaster()->create();

        $payload = [
            'session_number' => 1,
            'title' => 'Session 1',
            'scheduled_at' => now()->format('Y-m-d H:i:s'),
            'duration' => 60,
            'location' => 'Room A',
            'notes' => 'Lorem ipsum dolor sit amet.',
        ];

        $response = $this->actingAs($campaign->gameMaster)
            ->postJson("/api/campaigns/$campaign->slug/sessions?with=campaign", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'session_number' => $payload['session_number'],
                'title' => $payload['title'],
                'scheduled_at' => $payload['scheduled_at'],
                'duration' => $payload['duration'],
                'location' => $payload['location'],
                'notes' => $payload['notes'],
                'campaign' => [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'content' => $campaign->content,
                    'start_date' => $campaign->start_date,
                    'end_date' => $campaign->end_date,
                    'level' => $campaign->level,
                    'active' => $campaign->active,
                    'visibility' => $campaign->visibility->value,
                    'player_limit' => $campaign->player_limit,
                ],
            ],
        ]);

        $this->assertDatabaseHas('sessions', [
            'campaign_id' => $campaign->getKey(),
            'session_number' => $payload['session_number'],
            'title' => $payload['title'],
            'scheduled_at' => $payload['scheduled_at'],
            'duration' => $payload['duration'],
            'location' => $payload['location'],
            'notes' => $payload['notes'],
        ]);

        $session = Session::find($response->json('data.id'));

        $campaign->gameMaster->refresh();

        $this->assertTrue($campaign->gameMaster->can('update', $session));
        $this->assertTrue($campaign->gameMaster->can('delete', $session));
    }

}
