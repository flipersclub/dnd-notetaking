<?php

namespace Tests\Feature\Image;

use App\Models\Image\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageListTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $this->getJson('/api/images')
            ->assertUnauthorized();
    }

    public function test_it_returns_users_images_if_successful(): void
    {
        $user = User::factory()->create();
        $images = Image::factory(10)->for($user)->create();
        $otherImages = Image::factory(10)->create();

        $this->actingAs($user)
            ->getJson('/api/images')
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'name', 'thumbnail', 'original'
                    ]
                ]
            ])
            ->assertJsonCount(10, 'data')
            ->assertJson(['data' => $images->map(fn($image) => [
                'id' => $image->id,
                'name' => $image->name,
            ])->toArray()])
            ->assertJsonMissing(['data' => $otherImages->map(fn($image) => [
                'id' => $image->id,
                'name' => $image->name,
            ])->toArray()]);
    }
}
