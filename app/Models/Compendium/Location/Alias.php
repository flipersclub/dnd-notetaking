<?php

namespace App\Models\Compendium\Location;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alias extends Model
{
    use HasFactory;

    protected $table = 'location_aliases';

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
