<?php

namespace App\Models\Compendium\Calendar;

use App\Models\Compendium\Compendium;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Compendium $compendium
 */
class Calendar extends Model
{
    use HasFactory;

    public function compendium(): BelongsTo
    {
        return $this->belongsTo(Compendium::class);
    }
}
