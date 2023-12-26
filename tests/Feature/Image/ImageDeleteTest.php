<?php

namespace Tests\Feature\Image;

use App\Actions\Image\CreateImageForUser;
use App\Models\Image\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_not_logged_in(): void
    {
        $this->deleteJson('/api/images/1')
            ->assertUnauthorized();
    }

    public function test_it_returns_forbidden_if_user_not_logged_in(): void
    {
        $image = Image::factory()->create();
        $this->signedIn()
            ->deleteJson("/api/images/$image->id")
            ->assertForbidden();
    }

    public function test_it_deletes_image()
    {
        Storage::fake();

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('image.jpg');
        $image = CreateImageForUser::run($user, [], $file);

        $this->assertDatabaseHas('images', [
            'id' => $image->id
        ]);
        $this->assertTrue(
            Storage::fileExists("images/$image->id/original-$image->name.$image->extension")
        );
        $this->assertTrue(
            Storage::fileExists("images/$image->id/thumbnail-$image->name.$image->extension")
        );

        $this->actingAs($image->user)
            ->deleteJson("/api/images/$image->id")
            ->assertSuccessful()
            ->assertNoContent();

        $this->assertDatabaseMissing('images', [
            'id' => $image->id
        ]);
        $this->assertFalse(
            Storage::fileExists("images/$image->id/original-$image->name.$image->extension")
        );
        $this->assertFalse(
            Storage::fileExists("images/$image->id/thumbnail-$image->name.$image->extension")
        );
    }
}
