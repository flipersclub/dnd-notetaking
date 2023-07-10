<?php

namespace App\Observers;

use App\Models\Image\Image;
use Illuminate\Support\Facades\Storage;

class ImageObserver
{
    /**
     * Handle the ImageImage "created" event.
     */
    public function created(Image $image): void
    {
        //
    }

    /**
     * Handle the ImageImage "updated" event.
     */
    public function updated(Image $image): void
    {
        //
    }

    /**
     * Handle the ImageImage "deleted" event.
     */
    public function deleted(Image $image): void
    {
        if (Storage::exists($image->name)) {
            Storage::delete($image->name);
        }
    }

    /**
     * Handle the ImageImage "restored" event.
     */
    public function restored(Image $image): void
    {
        //
    }

    /**
     * Handle the ImageImage "force deleted" event.
     */
    public function forceDeleted(Image $image): void
    {
        //
    }
}
