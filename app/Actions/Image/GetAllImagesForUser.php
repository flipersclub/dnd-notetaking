<?php

namespace App\Actions\Image;

use App\Models\Image\Image;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class GetAllImagesForUser
{
    use AsAction;

    public function handle(User $user, array $with = [], array $columns = ['*'])
    {
        return Image::where('user_id', $user->id)
            ->with($with)
            ->get($columns);
    }
}
