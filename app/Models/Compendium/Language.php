<?php

namespace App\Models\Compendium;

use App\Models\HasTags;
use App\Models\Tag;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property ?string $slug
 * @property int $compendium_id
 * @property string $name
 * @property ?string $content
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Compendium $compendium
 * @property Collection<Tag> $tags
 */
class Language extends Model
{
    use HasFactory, HasTags, Sluggable, SluggableScopeHelpers;

    protected $guarded = ['id'];

    public function compendium(): BelongsTo
    {
        return $this->belongsTo(Compendium::class);
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
