<?php

namespace Tests\Feature\System;

use App\Models\System;
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

class SystemCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_redirect_if_user_not_logged_in(): void
    {
        $response = $this->postJson('/api/systems');

        $response->assertUnauthorized();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_see(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->postJson('/api/systems');

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $user = $this->userWithRole('systems.create', 'admin');

        $response = $this->actingAs($user)
                         ->postJson('/api/systems', $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseEmpty('systems');

    }

    public static function validationDataProvider()
    {
        return [
            'name not present' => [[], ['name' => 'The name field is required.']],
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 characters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 255 characters' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.']],
        ];
    }

    public function test_it_returns_successful_if_systems_returned(): void
    {
        $user = $this->userWithRole('systems.create', 'admin');

        $response = $this->actingAs($user)
                         ->postJson('/api/systems', [
                             'name' => 'D&D',
                             'content' => ($content = Str::random(65535))
                         ]);

        $response->assertSuccessful();

        $system = System::findBySlug($response->json('data.slug'));

        $this->assertInstanceOf(System::class, $system);

        $response->assertJson([
            'data' => [
                'name' => 'D&D',
                'content' => $content
            ]
        ]);

        $this->assertDatabaseHas('systems', [
            'name' => 'D&D',
            'content' => $content,
        ]);

    }
}
