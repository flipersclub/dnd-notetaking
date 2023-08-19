<?php

namespace Tests\Feature\Notebook\Note;

use App\Models\Notebook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NoteCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in()
    {
        $notebook = Notebook::factory()->create();
        $this->postJson("api/notebooks/$notebook->slug/notes")
            ->assertUnauthorized();
    }

    public function test_it_returns_forbidden_if_user_doesnt_own_notebook()
    {
        $notebook = Notebook::factory()->create();
        $this->signedIn()
            ->postJson("api/notebooks/$notebook->slug/notes")
            ->assertForbidden();
    }

    public function test_it_returns_not_found_if_notebook_invalid()
    {
        $this->signedIn()
            ->postJson("api/notebooks/lalalala/notes")
            ->assertNotFound();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $notebook = Notebook::factory()->create();

        $response = $this->asAdmin()
            ->postJson("api/notebooks/$notebook->slug/notes", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseEmpty('notes');

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

    public function test_it_creates_note_if_successful(): void
    {
        $notebook = Notebook::factory()->create();

        $response = $this->actingAs($notebook->user)
            ->postJson("api/notebooks/$notebook->slug/notes?with=notebook", [
                'name' => 'Ideas',
                'content' => ($content = Str::random(65535)),
            ]);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => 'Ideas',
                'content' => $content,
                'notebook' => [
                    'id' => $notebook->id,
                    'name' => $notebook->name,
                    'content' => $notebook->content
                ]
            ]
        ]);

        $this->assertDatabaseHas('notes', [
            'name' => 'Ideas',
            'notebook_id' => $notebook->getKey(),
            'content' => $content,
        ]);

    }

}
