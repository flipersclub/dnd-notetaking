<?php

namespace App\Actions\Campaign;

use App\Models\Compendium\Compendium;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class GetAllCampaignsForUser
{
    use AsAction;

    public function handle(User $user, array $with = [], array $columns = ['*'])
    {
        $campaignsAllowedToSee = $user->permissions()
                                      ->where('name', 'like', 'campaign.view.%')
                                      ->pluck('name')
                                      ->map(fn($name) => Str::remove('campaign.view.', $name));
        return Compendium::where(function (Builder $query) use ($user, $campaignsAllowedToSee) {
            return $query->where('game_master_id', $user->getKey())
                         ->orWhereIn('id', $campaignsAllowedToSee);
        })
                         ->with($with)
                         ->get($columns);
    }

}
