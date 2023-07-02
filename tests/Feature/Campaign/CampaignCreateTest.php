<?php

namespace Tests\Feature\Campaign;

use App\Enums\CampaignVisibility;
use App\Models\Campaign;
use App\Models\Setting;
use App\Models\System;
use App\Models\Tag;
use App\Models\User;
use Faker\Provider\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CampaignCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
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
        $user = $this->userWithRole('campaigns.create', 'admin');

        $response = $this->actingAs($user)
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
            'description not a string' => [['description' => ['an', 'array']], ['description' => 'The description field must be a string.']],
            'description longer than 65535 characters' => [['description' => Str::random(65536)], ['description' => 'The description field must not be greater than 65535 characters.'],],
            'start_date not a valid date' => [['start_date' => 'invalid-date'], ['start_date' => 'The start date field must be a valid date.']],
            'end_date not a valid date' => [['end_date' => 'invalid-date'], ['end_date' => 'The end date field must be a valid date.']],
            'end_date before start_date' => [['start_date' => '2023-01-01', 'end_date' => '2022-12-31'], ['end_date' => 'The end date field must be a date after or equal to start date.']],
            'game_master_id not a valid user ID' => [['game_master_id' => 999], ['game_master_id' => 'The selected game master id is invalid.']],
            'level not an integer' => [['level' => 'not-an-integer'], ['level' => 'The level field must be an integer.']],
            'level less than 1' => [['level' => 0], ['level' => 'The level field must be at least 1.']],
            'system_id not a valid system ID' => [['system_id' => 999], ['system_id' => 'The selected system id is invalid.']],
            'setting_id not a valid setting ID' => [['setting_id' => 999], ['setting_id' => 'The selected setting id is invalid.']],
            'visibility not one of the allowed values' => [['visibility' => 'invalid-visibility'], ['visibility' => 'The selected visibility is invalid.']],
            'player_limit not an integer' => [['player_limit' => 'not-an-integer'], ['player_limit' => 'The player limit field must be an integer.']],
            'player_limit less than 1' => [['player_limit' => 0], ['player_limit' => 'The player limit field must be at least 1.']],
            'cover_image not an image file' => [['cover_image' => UploadedFile::fake()->create('document.pdf')], ['cover_image' => 'The cover image field must be an image.']],
            'cover_image larger than 2MB' => [['cover_image' => UploadedFile::fake()->image('avatar.jpg')->size(3000)], ['cover_image' => 'The cover image field must not be greater than 2048 kilobytes.']],
            'tags not an array' => [['tags' => 'not-an-array'], ['tags' => 'The tags field must be an array.']],
            'tags.* not a valid tag ID' => [['tags' => [999]], ['tags.0' => 'The selected tags.0 is invalid.']],
        ];
    }

    public function test_it_returns_successful_if_campaigns_returned(): void
    {
        $user = $this->userWithRole('campaigns.create', 'admin');

        $file = UploadedFile::fake()->image('avatar.jpg', 1020, 100);

        Storage::fake();
        Carbon::setTestNow(now());

        $system = System::factory()->create();
        $setting = Setting::factory()->create();
        $tag = Tag::factory()->create();

        $payload = [
            'name' => 'Whenüa',
            'description' => Str::random(65535),
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'game_master_id' => $user->id,
            'level' => 5,
            'system_id' => $system->id,
            'setting_id' => $setting->id,
            'visibility' => CampaignVisibility::public->value,
            'player_limit' => 10,
            'cover_image' => $file,
            'tags' => [$tag->id],
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/campaigns?with=tags,system,setting,gameMaster', $payload);

        $response->assertSuccessful();

        Storage::assertExists('campaigns/' . $file->hashName());

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'description' => $payload['description'],
                'start_date' => $payload['start_date'],
                'end_date' => $payload['end_date'],
                'level' => $payload['level'],
                'visibility' => $payload['visibility'],
                'player_limit' => $payload['player_limit'],
                'cover_image' => env('APP_URL') . '/campaigns/' . $file->hashName() . '?expiration=' . now()->addMinutes(5)->timestamp,
                'gameMaster' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'system' => [
                    'id' => $system->id,
                    'name' => $system->name
                ],
                'setting' => [
                    'id' => $setting->id,
                    'name' => $setting->name
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
            'description' => $payload['description'],
            'start_date' => $payload['start_date'],
            'end_date' => $payload['end_date'],
            'game_master_id' => $payload['game_master_id'],
            'level' => $payload['level'],
            'system_id' => $payload['system_id'],
            'setting_id' => $payload['setting_id'],
            'visibility' => $payload['visibility'],
            'player_limit' => $payload['player_limit'],
            'cover_image' => 'campaigns/' . $file->hashName(),
        ]);

        $campaign = Campaign::find($response->json('data')['id']);

        $user->refresh();

        $this->assertTrue($user->can('update', $campaign));
        $this->assertTrue($user->can('delete', $campaign));
    }


}
