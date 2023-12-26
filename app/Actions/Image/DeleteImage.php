<?php

namespace App\Actions\Image;

use App\Models\Image\Image;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteImage
{
    use AsAction;

    public function handle(Image $image)
    {
        if ($image->delete()) {
            Storage::delete("/images/$image->id/original-$image->name.$image->extension");
            Storage::delete("/images/$image->id/thumbnail-$image->name.$image->extension");
            return true;
        }
        return false;
    }
}
