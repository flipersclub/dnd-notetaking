<?php

namespace App\Models;

use App\Enums\ImageType;
use App\Models\Image\Image;
use App\Models\Image\Imageable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class System extends Model
{
    use HasFactory, HasTags;

    protected $guarded = ['id'];

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable')
            ->using(Imageable::class)
            ->withPivot('type_id')
            ->withTimestamps();
    }

    public function coverImage(): HasOneThrough
    {
        return $this->hasOneThrough(
            Image::class,
            Imageable::class,
            firstKey: 'imageable_id',
            secondKey: 'id',
            localKey: 'id',
            secondLocalKey: 'image_id'
        )
            ->where('imageable_type', $this->getMorphClass())
            ->where('type_id', ImageType::cover->value);
    }
}
