<?php

namespace Tests\Feature\Image;

use App\Models\Image\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImageUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $this->putJson('/api/images/1')
            ->assertUnauthorized();
    }

    public function test_it_returns_forbidden_if_user_not_logged_in(): void
    {
        $image = Image::factory()->create();
        $this->signedIn()
            ->putJson("/api/images/$image->id")
            ->assertForbidden();
    }

    public function test_it_returns_unprocessable_if_name_not_a_string(): void
    {
        $image = Image::factory()->create();
        $this->actingAs($image->user)
            ->putJson("/api/images/$image->id", ['name' => ['not a string']])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name' => 'The name field must be a string.'
            ]);
    }

    public function test_it_can_update_image_data()
    {
        $image = Image::factory()->create();
        $this->actingAs($image->user)
            ->putJson("/api/images/$image->id", ['name' => 'New name'])
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['id', 'name']])
            ->assertJson([
                'data' => [
                    'id' => $image->id,
                    'name' => 'New name',
                ]
            ]);
    }
}
