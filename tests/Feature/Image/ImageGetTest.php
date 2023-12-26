<?php

namespace Tests\Feature\Image;

use App\Models\Image\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImageGetTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $this->getJson('/api/images/1')
            ->assertUnauthorized();
    }

    public function test_it_returns_forbidden_if_user_not_logged_in(): void
    {
        $image = Image::factory()->create();
        $this->signedIn()
            ->getJson("/api/images/$image->id")
            ->assertForbidden();
    }

    public function test_it_gets_image_data()
    {
        $image = Image::factory()->create();
        $this->actingAs($image->user)
            ->getJson("/api/images/$image->id")
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['id', 'name', 'thumbnail', 'original']])
            ->assertJson([
                'data' => [
                    'id' => $image->id,
                    'name' => $image->name,
                ]
            ]);
    }
}
