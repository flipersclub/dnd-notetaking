<?php

namespace Tests\Feature\Campaign;

use App\Enums\CampaignVisibility;
use App\Models\Campaign;
use App\Models\Compendium\Compendium;
use App\Models\System;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CampaignCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $response = $this->postJson('/api/campaigns');

        $response->assertUnauthorized();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_see(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/campaigns');

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $response = $this->asAdmin()
            ->postJson('/api/campaigns', $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseEmpty('campaigns');

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
            'start_date not a valid date' => [['start_date' => 'invalid-date'], ['start_date' => 'The start date field must be a valid date.']],
            'end_date not a valid date' => [['end_date' => 'invalid-date'], ['end_date' => 'The end date field must be a valid date.']],
            'end_date before start_date' => [['start_date' => '2023-01-01', 'end_date' => '2022-12-31'], ['end_date' => 'The end date field must be a date after or equal to start date.']],
            'game_master_id not a valid user ID' => [['game_master_id' => 999], ['game_master_id' => 'The selected game master id is invalid.']],
            'level not an integer' => [['level' => 'not-an-integer'], ['level' => 'The level field must be an integer.']],
            'level less than 1' => [['level' => 0], ['level' => 'The level field must be at least 1.']],
            'system_id not a valid system ID' => [['system_id' => 999], ['system_id' => 'The selected system id is invalid.']],
            'compendium_id not a valid compendium ID' => [['compendium_id' => 999], ['compendium_id' => 'The selected compendium id is invalid.']],
            'visibility not one of the allowed values' => [['visibility' => 'invalid-visibility'], ['visibility' => 'The selected visibility is invalid.']],
            'player_limit not an integer' => [['player_limit' => 'not-an-integer'], ['player_limit' => 'The player limit field must be an integer.']],
            'player_limit less than 1' => [['player_limit' => 0], ['player_limit' => 'The player limit field must be at least 1.']],
            'tags not an array' => [['tags' => 'not-an-array'], ['tags' => 'The tags field must be an array.']],
            'tags.* not a valid tag ID' => [['tags' => [999]], ['tags.0' => 'The selected tags.0 is invalid.']],
        ];
    }

    public function test_it_returns_successful_if_campaigns_returned(): void
    {
        $user = User::factory()->create();

        $system = System::factory()->create();
        $compendium = Compendium::factory()->create();
        $tag = Tag::factory()->create();

        $payload = [
            'name' => 'Whenüa',
            'content' => Str::random(65535),
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'game_master_id' => $user->id,
            'level' => 5,
            'system_id' => $system->id,
            'compendium_id' => $compendium->id,
            'visibility' => CampaignVisibility::public->value,
            'player_limit' => 10,
            'tags' => [$tag->id],
        ];

        $response = $this->asAdmin()
            ->postJson('/api/campaigns?include=tags,system,compendium,gameMaster', $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'content' => $payload['content'],
                'start_date' => $payload['start_date'],
                'end_date' => $payload['end_date'],
                'level' => $payload['level'],
                'visibility' => $payload['visibility'],
                'player_limit' => $payload['player_limit'],
                'gameMaster' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'system' => [
                    'id' => $system->id,
                    'name' => $system->name
                ],
                'compendium' => [
                    'id' => $compendium->id,
                    'name' => $compendium->name
                ],
                'tags' => [
                    [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('campaigns', [
            'name' => $payload['name'],
            'content' => $payload['content'],
            'start_date' => $payload['start_date'],
            'end_date' => $payload['end_date'],
            'game_master_id' => $payload['game_master_id'],
            'level' => $payload['level'],
            'system_id' => $payload['system_id'],
            'compendium_id' => $payload['compendium_id'],
            'visibility' => $payload['visibility'],
            'player_limit' => $payload['player_limit'],
        ]);

        $campaign = Campaign::find($response->json('data')['id']);

        $user->refresh();

        $this->assertTrue($user->can('update', $campaign));
        $this->assertTrue($user->can('delete', $campaign));
    }


}
