<?php

namespace App\Models;

use App\Enums\CampaignVisibility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasUuids, HasFactory, HasTags;

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean',
        'visibility' => CampaignVisibility::class
    ];

    public function gameMaster(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }
}
