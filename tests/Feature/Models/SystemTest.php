<?php

namespace Tests\Feature\Models;

use App\Enums\ImageType;
use App\Models\Image\Image;
use App\Models\System;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_have_images(): void
    {
        /** @var System $system */
        $system = System::factory()->create();

        $coverImage = Image::factory()->create();
        $system->images()->attach($coverImage, ['type_id' => ImageType::cover->value]);

        $this->assertInstanceOf(Image::class, $system->coverImage);
        $this->assertTrue($system->coverImage->is($coverImage));

        $otherImage = Image::factory()->create();
        $system->images()->attach($otherImage);

        $this->assertCount(2, $system->images);
        foreach ($system->images as $image) {
            $this->assertInstanceOf(Image::class, $image);
        }
    }
}
