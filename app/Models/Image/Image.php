<?php

namespace App\Models\Image;

use App\Models\System;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function systems()
    {
        return $this->morphedByMany(System::class, 'imageable')
            ->using(Imageable::class)
            ->withPivot('type_id')
            ->withTimestamps();
    }
}
