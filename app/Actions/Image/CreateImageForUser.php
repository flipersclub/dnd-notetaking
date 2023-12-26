<?php

namespace App\Actions\Image;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateImageForUser
{
    use AsAction;

    public function handle(User $user, array $data, UploadedFile $file, array $with = [])
    {
        $image = $user->images()->create([
            'name' => $data['name'] ?? $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension()
        ]);

        Storage::putFileAs("images/$image->id", $file, "original-$image->name.$image->extension");
        // make thumbnail
        $thumbnail = ImageManager::imagick()->read($file);
        $thumbnail->scaleDown(250, 250);
        Storage::putFileAs("images/$image->id", $file, "thumbnail-$image->name.$image->extension");

        return $image->load($with);
    }
}
