<?php

namespace Tests\Feature\Compendium\Language;

use App\Models\Compendium\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LanguageDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $language = Language::factory()->create();

        $response = $this->deleteJson("/api/languages/$language->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_language_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/languages/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_language(): void
    {
        $user = User::factory()->create();

        $language = Language::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/languages/$language->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_language_deleted(): void
    {
        $language = Language::factory()->create();

        $response = $this->actingAs($language->compendium->creator)
            ->deleteJson("/api/languages/$language->slug");

        $response->assertNoContent();

        $this->assertModelMissing($language);

    }
}
