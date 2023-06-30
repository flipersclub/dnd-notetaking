<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class System extends Model
{
    use HasFactory, HasTags;

    protected $guarded = ['id'];

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
}
