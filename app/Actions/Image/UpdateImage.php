<?php

namespace App\Actions\Image;

use App\Models\Image\Image;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateImage
{
    use AsAction;

    public function handle(Image $image, array $data, array $with = [])
    {
        $image->update($data);
        return $image->load($with);
    }
}
