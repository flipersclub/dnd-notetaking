<?php

namespace Tests\Feature\Image;

use App\Actions\Image\CreateImageForUser;
use App\Models\Image\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $this->getJson("/api/images/1/download")
            ->assertUnauthorized();
    }

    public function test_it_downloads_image_data()
    {
        Storage::fake();

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('image.jpg');
        $image = CreateImageForUser::run($user, [], $file);

        $this->assertTrue(
            Storage::fileExists("images/$image->id/original-$image->name.$image->extension")
        );
        $this->assertTrue(
            Storage::fileExists("images/$image->id/thumbnail-$image->name.$image->extension")
        );

        $this->signedIn()
            ->getJson("/api/images/$image->id/download")
            ->assertSuccessful()
            ->assertDownload("original-$image->name.$image->extension");
    }

    public function test_it_downloads_thumbnail()
    {
        Storage::fake();

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('image.jpg');
        $image = CreateImageForUser::run($user, [], $file);

        $this->assertTrue(
            Storage::fileExists("images/$image->id/original-$image->name.$image->extension")
        );
        $this->assertTrue(
            Storage::fileExists("images/$image->id/thumbnail-$image->name.$image->extension")
        );

        $this->signedIn()
            ->getJson("/api/images/$image->id/download?type=thumbnail")
            ->assertSuccessful()
            ->assertDownload("thumbnail-$image->name.$image->extension");
    }
}
