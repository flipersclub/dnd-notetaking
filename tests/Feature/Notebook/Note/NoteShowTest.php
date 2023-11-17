<?php

namespace Tests\Feature\Notebook\Note;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NoteShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $note = Note::factory()->create();

        $response = $this->getJson("/api/notes/$note->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_note_not_existent(): void
    {
        $response = $this->signedIn()
            ->getJson("/api/notes/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_note(): void
    {
        $note = Note::factory()->create();

        $response = $this->signedIn()
            ->getJson("/api/notes/$note->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_note_returned(): void
    {
        $note = Note::factory()->create();

        $response = $this->actingAs($note->notebook->user)
            ->getJson("/api/notes/$note->slug?include=notebook");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'id' => $note->id,
                'slug' => $note->slug,
                'name' => $note->name,
                'content' => $note->content,
                'notebook' => [
                    'id' => $note->notebook->id,
                    'slug' => $note->notebook->slug,
                    'name' => $note->notebook->name,
                    'content' => $note->notebook->content,
                ]
            ]
        ]);

    }

    public function test_it_returns_successful_if_note_returned_with_user(): void
    {
        $note = Note::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/notes/$note->slug?include=notebook");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'id' => $note->id,
                'slug' => $note->slug,
                'name' => $note->name,
                'content' => $note->content,
                'notebook' => [
                    'id' => $note->notebook->id,
                    'name' => $note->notebook->name,
                    'content' => $note->notebook->content
                ]
            ]
        ]);

    }
}
