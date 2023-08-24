<?php

namespace Tests\Feature\Compendium\Item;

use App\Models\Compendium\Item;
use App\Models\Compendium\Species;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ItemUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $item = Item::factory()->create();

        $response = $this->putJson("/api/items/$item->slug");

        $response->assertUnauthorized();
    }

    public function test_it_returns_not_found_if_item_not_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/items/lalalalala");

        $response->assertNotFound();
    }

    public function test_it_returns_forbidden_if_user_not_allowed_to_update_item(): void
    {
        $user = User::factory()->create();

        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/items/$item->slug");

        $response->assertForbidden();
    }

    /** @dataProvider validationDataProvider */
    public function test_it_returns_unprocessable_if_validation_failed($payload, $errors): void
    {
        $item = Item::factory()->create();

        $response = $this->asAdmin()
            ->putJson("/api/items/$item->slug", $payload);

        $response->assertUnprocessable();

        $response->assertInvalid($errors);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            // Ensure the original data is not modified
            'name' => $item->name,
            'content' => $item->content,
            // ... add more fields as needed
        ]);
    }

    public static function validationDataProvider()
    {
        return [
            'name empty' => [['name' => ''], ['name' => 'The name field is required.']],
            'name not a string' => [['name' => ['an', 'array']], ['name' => 'The name field must be a string.']],
            'name longer than 255 items' => [['name' => Str::random(256)], ['name' => 'The name field must not be greater than 255 items.']],
            'content not a string' => [['content' => ['an', 'array']], ['content' => 'The content field must be a string.']],
            'content longer than 65535 items' => [['content' => Str::random(65536)], ['content' => 'The content field must not be greater than 65535 items.'],],
        ];
    }

    public function test_it_returns_successful_if_item_updated_returned(): void
    {
        $item = Item::factory()->create();

        $species = Species::factory()->for($item->compendium)->create();
        $payload = [
            'name' => 'John Doe',
            'content' => Str::random(65535),
        ];

        $response = $this->actingAs($item->compendium->creator)
            ->putJson("/api/items/$item->slug?with=tags,compendium", $payload);

        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $payload['name'],
                'age' => $payload['age'],
                'gender' => $payload['gender'],
                'content' => $payload['content'],
                'compendium' => [
                    'id' => $item->compendium->id,
                    'name' => $item->compendium->name
                ]
            ]
        ]);

        $this->assertDatabaseHas('items', [
            'name' => $payload['name'],
            'content' => $payload['content'],
        ]);

        $item->refresh();

        $this->assertTrue($item->compendium->creator->can('update', $item));
        $this->assertTrue($item->compendium->creator->can('delete', $item));
    }
}
