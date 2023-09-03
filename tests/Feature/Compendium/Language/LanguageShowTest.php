<?php

namespace Tests\Feature\Compendium\Language;

use App\Models\Compendium\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LanguageShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $language = Language::factory()->create();

        $response = $this->getJson("/api/languages/$language->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_language_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/languages/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_language(): void
    {
        $user = User::factory()->create();

        $language = Language::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/languages/$language->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_language(): void
    {
        $language = Language::factory()->create();

        $response = $this->actingAs($language->compendium->creator)
            ->getJson("/api/languages/$language->slug?with=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $language->name,
                'content' => $language->content,
                'compendium' => [
                    'id' => $language->compendium->id,
                    'name' => $language->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_language(): void
    {
        $language = Language::factory()->create();

        $user = $this->userWithPermission("languages.view.$language->id");

        $response = $this->actingAs($user)
            ->getJson("/api/languages/$language->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_language(): void
    {
        $language = Language::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/languages/$language->slug");

        $response->assertSuccessful();

    }
}
