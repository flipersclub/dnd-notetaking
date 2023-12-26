<?php

namespace Tests\Feature\Image;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $this->postJson('/api/images')
            ->assertUnauthorized();
    }

    public function test_it_returns_unprocessable_if_not_an_image(): void
    {
        $file = UploadedFile::fake()->create('poop.pdf');
        $this->signedIn()
            ->postJson('/api/images', [
                'image' => $file
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'image' => 'The image field must be an image.'
            ]);
    }

    public function test_it_returns_unprocessable_if_image_is_more_than_1_mb(): void
    {
        $file = UploadedFile::fake()->create('poop.jpg', kilobytes: 3000);
        $this->signedIn()
            ->postJson('/api/images', [
                'image' => $file
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'image' => 'The image field must not be greater than 1024 kilobytes.'
            ]);
    }

    public function test_it_returns_unprocessable_if_image_has_the_wrong_dimensions(): void
    {
        $file = UploadedFile::fake()->image('poop.jpg', 3000, 3000);
        $this->signedIn()
            ->postJson('/api/images', [
                'image' => $file
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'image' => 'The image field has invalid image dimensions.'
            ]);
    }

    public function test_it_returns_unprocessable_if_image_name_is_not_a_string(): void
    {
        $file = UploadedFile::fake()->image('poop.jpg', 3000, 3000);
        $this->signedIn()
            ->postJson('/api/images', [
                'name' => ['an array']
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name' => 'The name field must be a string.'
            ]);
    }

    public function test_it_creates_images()
    {
        Storage::fake();

        $name = 'testimage';
        $file = UploadedFile::fake()->image('othername.jpg');

        $response = $this->signedIn()
            ->postJson('/api/images', [
                'name' => $name,
                'image' => $file
            ])
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['id', 'name']])
            ->assertJson([
                'data' => [
                    'name' => $name
                ]
            ]);

        Storage::disk()->assertExists("/images/{$response->json('data.id')}/original-testimage.jpg");
        Storage::disk()->assertExists("/images/{$response->json('data.id')}/thumbnail-testimage.jpg");
    }
}
