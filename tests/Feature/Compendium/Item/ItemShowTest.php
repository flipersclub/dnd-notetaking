<?php

namespace Tests\Feature\Compendium\Item;

use App\Models\Compendium\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $item = Item::factory()->create();

        $response = $this->getJson("/api/items/$item->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_item_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/items/lalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_see_item(): void
    {
        $user = User::factory()->create();

        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/items/$item->slug");

        $response->assertForbidden();
    }

    public function test_compendium_creator_can_see_item(): void
    {
        $item = Item::factory()->create();

        $response = $this->actingAs($item->compendium->creator)
            ->getJson("/api/items/$item->slug?with=compendium");

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $item->name,
                'content' => $item->content,
                'compendium' => [
                    'id' => $item->compendium->id,
                    'name' => $item->compendium->name
                ]
            ]
        ]);

    }

    public function test_user_with_permission_can_see_item(): void
    {
        $item = Item::factory()->create();

        $user = $this->userWithPermission("items.view.$item->id");

        $response = $this->actingAs($user)
            ->getJson("/api/items/$item->slug");

        $response->assertSuccessful();

    }

    public function test_admin_can_see_item(): void
    {
        $item = Item::factory()->create();

        $response = $this->asAdmin()
            ->getJson("/api/items/$item->slug");

        $response->assertSuccessful();

    }
}
