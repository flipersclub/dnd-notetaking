<?php

namespace App\Models;

use App\Enums\ImageType;
use App\Models\Image\Image;
use App\Models\Image\Imageable;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Note extends Model
{
    use HasFactory, HasTags, Sluggable, SluggableScopeHelpers;

    protected $guarded = ['id'];

    public function notebook(): BelongsTo
    {
        return $this->belongsTo(Notebook::class);
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

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function getRouteKeyName()
    {
        return $this->getSlugKeyName();
    }
}
