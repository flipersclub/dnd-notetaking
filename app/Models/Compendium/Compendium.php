<?php

namespace App\Models\Compendium;

use App\Models\Campaign;
use App\Models\Compendium\Calendar\Calendar;
use App\Models\Compendium\Calendar\Event;
use App\Models\Compendium\Location\Location;
use App\Models\Encounter;
use App\Models\HasTags;
use App\Models\Quest;
use App\Models\User;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Permission;

/**
 * @property User $creator
 */
class Compendium extends Model
{
    use HasFactory, HasTags, Sluggable, SluggableScopeHelpers;

    protected $guarded = ['id'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }

    public function species(): HasMany
    {
        return $this->hasMany(Species::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function factions(): HasMany
    {
        return $this->hasMany(Faction::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function concepts(): HasMany
    {
        return $this->hasMany(Concept::class);
    }

    public function naturalResources(): HasMany
    {
        return $this->hasMany(NaturalResource::class);
    }

    public function currencies(): HasMany
    {
        return $this->hasMany(Currency::class);
    }

    public function languages(): HasMany
    {
        return $this->hasMany(Language::class);
    }

    public function calendars(): HasMany
    {
        return $this->hasMany(Calendar::class);
    }

    public function religions(): HasMany
    {
        return $this->hasMany(Religion::class);
    }

    public function pantheons(): HasMany
    {
        return $this->hasMany(Religion::class);
    }

    public function encounters(): HasMany
    {
        return $this->hasMany(Encounter::class);
    }

    public function quests(): HasMany
    {
        return $this->hasMany(Quest::class);
    }

    public function spells(): HasMany
    {
        return $this->hasMany(Encounter::class);
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
