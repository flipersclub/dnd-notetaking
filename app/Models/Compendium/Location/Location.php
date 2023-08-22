<?php

namespace App\Models\Compendium\Location;

use App\Models\Compendium\Compendium;
use App\Models\HasTags;
use App\Models\Pivots\LocationLocationService;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property ?string $slug
 * @property int $compendium_id
 * @property ?int $parent_id
 * @property string $name
 * @property int $type_id
 * @property ?string $content
 * @property ?string $demonym
 * @property ?string $government_type_id
 * @property ?int $population
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Compendium $compendium
 * @property ?Location $parent
 * @property Collection<Location> $children
 * @property Type $type
 * @property ?GovernmentType $governmentType
 * @property Collection<Alias> $aliases
 * @property Collection<Service> $services
 * @property Collection<Map> $maps
 */
class Location extends Model
{
    use HasFactory, HasTags, Sluggable, SluggableScopeHelpers;

    protected $guarded = ['id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function governmentType(): BelongsTo
    {
        return $this->belongsTo(GovernmentType::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(Alias::class);
    }

    public function services(): HasManyThrough
    {
        return $this->hasManyThrough(Service::class, LocationLocationService::class);
    }

    public function maps(): HasMany
    {
        return $this->hasMany(Map::class);
    }

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
