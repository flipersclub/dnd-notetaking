<?php

namespace Tests\Feature\Notebook\Note;

use App\Models\Note;
use App\Models\Notebook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NoteUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $note = Note::factory()->create();

        $response = $this->putJson("/api/notes/$note->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_note_not_existent(): void
    {
        $response = $this->signedIn()
            ->putJson("/api/notes/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_note(): void
    {
        $note = Note::factory()->create();

        $response = $this->signedIn()
            ->putJson("/api/notes/$note->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $note = Note::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/notes/$note->slug", $payload);

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
            'notebookId invalid' => [['notebookId' => 999999], ['notebook_id' => 'The selected notebook id is invalid.']],
        ];
    }

    public function test_it_returns_unprocessable_if_new_notebook_does_not_belong_to_user(): void
    {
        $notebook = Notebook::factory()->create();
        $note = Note::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/notes/$note->slug", [
                'notebookId' => $notebook->id
            ]);

        $response->assertUnprocessable();

        $response->assertInvalid([
            'notebook_id' => 'The selected notebook id is invalid.'
        ]);
    }

    public function test_it_returns_successful_if_note_updated_returned(): void
    {
        $note = Note::factory()->create();
        $newNotebook = Notebook::factory()->for($note->notebook->user)->create();

        $response = $this->actingAs($note->notebook->user)
            ->putJson("/api/notes/$note->slug?with=notebook", [
                'name' => 'D&D',
                'content' => ($content = Str::random(65535)),
                'notebookId' => $newNotebook->id
            ]);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => 'D&D',
                'content' => $content,
                'notebook' => [
                    'id' => $newNotebook->id,
                    'name' => $newNotebook->name,
                    'content' => $newNotebook->content
                ]
            ]
        ]);

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'name' => 'D&D',
            'notebook_id' => $newNotebook->getKey(),
            'content' => $content,
        ]);

    }
}
