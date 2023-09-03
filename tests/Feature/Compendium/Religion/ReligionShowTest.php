<?php

namespace Tests\Feature\Compendium\Religion;

use App\Models\Compendium\Religion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReligionShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $religion = Religion::factory()->create();

        $response = $this->getJson("/api/religions/$religion->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_religion_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/religions/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_religion(): void
    {
        $user = User::factory()->create();

        $religion = Religion::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/religions/$religion->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_religion(): void
    {
        $religion = Religion::factory()->create();

        $response = $this->actingAs($religion->compendium->creator)
            ->getJson("/api/religions/$religion->slug?with=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $religion->name,
                'content' => $religion->content,
                'compendium' => [
                    'id' => $religion->compendium->id,
                    'name' => $religion->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_religion(): void
    {
        $religion = Religion::factory()->create();

        $user = $this->userWithPermission("religions.view.$religion->id");

        $response = $this->actingAs($user)
            ->getJson("/api/religions/$religion->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_religion(): void
    {
        $religion = Religion::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/religions/$religion->slug");

        $response->assertSuccessful();

    }
}
