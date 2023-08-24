<?php

namespace Tests\Feature\Compendium\Item;

use App\Models\Compendium\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemDestroyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $item = Item::factory()->create();

        $response = $this->deleteJson("/api/items/$item->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_item_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/items/99999999");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_delete_item(): void
    {
        $user = User::factory()->create();

        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/items/$item->slug");

        $response->assertForbidden();
    }

    public function test_it_returns_successful_if_item_deleted(): void
    {
        $item = Item::factory()->create();

        $response = $this->actingAs($item->compendium->creator)
            ->deleteJson("/api/items/$item->slug");

        $response->assertNoContent();

        $this->assertModelMissing($item);

    }
}
