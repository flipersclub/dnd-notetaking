<?php

namespace App\Models;

use App\Enums\ImageType;
use App\Models\Image\Image;
use App\Models\Image\Imageable;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;


/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $content
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection<Campaign> $campaigns
 * @property Collection<Image> $images
 * @property Image $coverImage
 * @property Collection<Tag> $tags
 */
class System extends Model
{
    use HasFactory, HasTags, Sluggable, SluggableScopeHelpers;

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
