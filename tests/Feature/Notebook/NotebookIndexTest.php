<?php

namespace Tests\Feature\Notebook;

use App\Models\Notebook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotebookIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $response = $this->getJson('/api/notebooks');

        $response->assertUnauthorized();
    }

    public function test_it_returns_successful_if_notebooks_returned(): void
    {
        $user = User::factory()->create();

        $notebooks = Notebook::factory(10)->for($user)->create();
        $otherNotebooks = Notebook::factory(10)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/notebooks');

        $response->assertSuccessful();

        $response->assertJsonCount(10, 'data');

        $response->assertJson([
            'data' => $notebooks->map(fn($notebook) => [
                'id' => $notebook->id,
                'name' => $notebook->name,
                'content' => $notebook->content
            ])->toArray()
        ]);

        $response->assertJsonMissing([
            'data' => $otherNotebooks->map(fn($notebook) => [
                'id' => $notebook->id,
                'name' => $notebook->name,
                'content' => $notebook->content
            ])->toArray()
        ]);

    }

}
