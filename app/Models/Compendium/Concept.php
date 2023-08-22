<?php

namespace App\Models\Compendium;

use App\Models\HasTags;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Compendium $compendium
 */
class Concept extends Model
{
    use HasFactory, HasTags;

    public function compendium(): BelongsTo
    {
        return $this->belongsTo(Compendium::class);
    }
}
