<?php

namespace Tests\Feature\Notebook;

use App\Models\Notebook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotebookUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $notebook = Notebook::factory()->create();

        $response = $this->putJson("/api/notebooks/$notebook->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_notebook_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/notebooks/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_notebook(): void
    {
        $user = User::factory()->create();

        $notebook = Notebook::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/notebooks/$notebook->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $notebook = Notebook::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/notebooks/$notebook->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 characters' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 characters.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 255 characters' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 characters.']],
        ];
    }

    public function test_it_returns_successful_if_notebook_updated_returned(): void
    {
        $notebook = Notebook::factory()->create();

        $response = $this->actingAs($notebook->user)
            ->putJson("/api/notebooks/$notebook->slug?include=user", [
                'name' => 'D&D',
                'content' => ($content = Str::random(65535)),
            ]);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => 'D&D',
                'content' => $content,
                'user' => [
                    'id' => $notebook->user->id,
                    'name' => $notebook->user->name,
                    'email' => $notebook->user->email
                ]
            ]
        ]);

        $this->assertDatabaseHas('notebooks', [
            'id' => $notebook->id,
            'name' => 'D&D',
            'user_id' => $notebook->user->getKey(),
            'content' => $content,
        ]);

    }
}
