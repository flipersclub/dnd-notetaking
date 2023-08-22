<?php

namespace App\Models;

use App\Enums\CampaignVisibility;
use App\Models\Compendium\Compendium;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property int $game_master_id
 * @property string $content
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property int $level
 * @property int $system_id
 * @property boolean $active
 * @property int $visibility
 * @property int $player_limit
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $gameMaster
 * @property System $system
 * @property Compendium $compendium
 * @property Collection<Session> $sessions
 * @property Collection<Encounter> $encounters
 * @property Collection<Tag> $tags
 */
class Campaign extends Model
{
    use HasFactory, HasTags, Sluggable, SluggableScopeHelpers;

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

    public function compendium(): BelongsTo
    {
        return $this->belongsTo(Compendium::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function encounters(): HasMany
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
