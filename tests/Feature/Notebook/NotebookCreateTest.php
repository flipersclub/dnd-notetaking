<?php

namespace Tests\Feature\Notebook;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotebookCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in()
    {
        $this->postJson('api/notebooks')
            ->assertUnauthorized();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $response = $this->asAdmin()
            ->postJson('/api/notebooks', $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseEmpty('notebooks');

    }

    public static function validationDataProvider()
    {
        return [
            'name not present' => [[], ['name' => 'The name field is required.']],
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 characters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 characters' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.']],
        ];
    }

    public function test_it_creates_notebook_if_successful(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/notebooks?include=user', [
                'name' => 'Ideas',
                'content' => ($content = Str::random(65535)),
            ]);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => 'Ideas',
                'content' => $content,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]
        ]);

        $this->assertDatabaseHas('notebooks', [
            'name' => 'Ideas',
            'user_id' => $user->getKey(),
            'content' => $content,
        ]);

    }

}
