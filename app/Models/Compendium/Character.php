<?php

namespace App\Models\Compendium;

use App\Models\HasTags;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Character extends Model
{
    use HasFactory, HasTags, Sluggable, SluggableScopeHelpers;

    protected $guarded = ['id'];

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
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
