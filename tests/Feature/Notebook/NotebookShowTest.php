<?php

namespace Tests\Feature\Notebook;

use App\Models\Notebook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotebookShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $notebook = Notebook::factory()->create();

        $response = $this->getJson("/api/notebooks/$notebook->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_notebook_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/notebooks/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_notebook(): void
    {
        $user = User::factory()->create();

        $notebook = Notebook::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/notebooks/$notebook->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_notebook_returned(): void
    {
        $notebook = Notebook::factory()->create();

        $response = $this->actingAs($notebook->user)
            ->getJson("/api/notebooks/$notebook->slug");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'id' => $notebook->id,
                'slug' => $notebook->slug,
                'name' => $notebook->name,
                'content' => $notebook->content
            ]
        ]);

    }

    public function test_it_returns_successful_if_notebook_returned_with_user(): void
    {
        $notebook = Notebook::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/notebooks/$notebook->slug?include=user");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'id' => $notebook->id,
                'slug' => $notebook->slug,
                'name' => $notebook->name,
                'content' => $notebook->content,
                'user' => [
                    'id' => $notebook->user->id,
                    'name' => $notebook->user->name,
                    'email' => $notebook->user->email
                ]
            ]
        ]);

    }
}
