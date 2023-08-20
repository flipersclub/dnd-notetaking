<?php

namespace Tests\Feature\Notebook\Note;

use App\Models\Note;
use App\Models\Notebook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $notebook = Notebook::factory()->create();

        $response = $this->getJson("/api/notebooks/$notebook->slug/notes");

        $response->assertUnauthorized();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see(): void
    {
        $notebook = Notebook::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/notebooks/$notebook->slug/notes");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_notes_returned(): void
    {
        $notebook = Notebook::factory()->create();

        $notes = Note::factory(10)->for($notebook)->create();
        $otherNotes = Note::factory(10)->create();

        $response = $this->actingAs($notebook->user)
            ->getJson("/api/notebooks/$notebook->slug/notes");

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');

        $response->assertJson([
            'data' => $notes->map(fn($note) => [
                'id' => $note->id,
                'name' => $note->name,
                'content' => $note->content
            ])->toArray()
        ]);

        $response->assertJsonMissing([
            'data' => $otherNotes->map(fn($note) => [
                'id' => $note->id,
                'name' => $note->name,
                'content' => $note->content
            ])->toArray()
        ]);

    }

}
