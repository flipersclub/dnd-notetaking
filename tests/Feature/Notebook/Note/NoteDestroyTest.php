<?php

namespace Tests\Feature\Notebook\Note;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NoteDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $note = Note::factory()->create();

        $response = $this->deleteJson("/api/notes/$note->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_note_not_existent(): void
    {
        $response = $this->signedIn()
            ->deleteJson("/api/notes/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_unauthorized_if_user_not_allowed_to_delete_note(): void
    {
        $note = Note::factory()->create();

        $response = $this->signedIn()
            ->deleteJson("/api/notes/$note->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_note_deleted(): void
    {
        $note = Note::factory()->create();

        $response = $this->actingAs($note->notebook->user)
            ->deleteJson("/api/notes/$note->slug");

        $response->assertNoContent();

        $this->assertModelMissing($note);

    }
}
