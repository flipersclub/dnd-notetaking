<?php

namespace App\Models\Image;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Imageable extends MorphPivot
{
    protected $table = 'imageables';

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
