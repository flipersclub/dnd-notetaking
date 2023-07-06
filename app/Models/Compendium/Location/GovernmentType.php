<?php

namespace App\Models\Compendium\Location;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GovernmentType extends Model
{
    use HasFactory;

    protected $table = 'location_government_types';

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }
}
