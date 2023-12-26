<?php

namespace App\Models\Image;

use App\Models\System;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $name
 * @property string $extension
 * @property User $user
 */
class Image extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function systems()
    {
        return $this->morphedByMany(System::class, 'imageable')
            ->using(Imageable::class)
            ->withPivot('type_id')
            ->withTimestamps();
    }
}
