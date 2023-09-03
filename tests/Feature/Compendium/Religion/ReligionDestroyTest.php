<?php

namespace Tests\Feature\Compendium\Religion;

use App\Models\Compendium\Religion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReligionDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $religion = Religion::factory()->create();

        $response = $this->deleteJson("/api/religions/$religion->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_religion_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/religions/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_religion(): void
    {
        $user = User::factory()->create();

        $religion = Religion::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/religions/$religion->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_religion_deleted(): void
    {
        $religion = Religion::factory()->create();

        $response = $this->actingAs($religion->compendium->creator)
            ->deleteJson("/api/religions/$religion->slug");

        $response->assertNoContent();

        $this->assertModelMissing($religion);

    }
}
