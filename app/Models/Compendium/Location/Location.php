<?php

namespace App\Models\Compendium\Location;

use App\Models\HasTags;
use App\Models\Pivots\LocationLocationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Location extends Model
{
    use HasFactory, HasTags;

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

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
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
}
