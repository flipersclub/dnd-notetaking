<?php

namespace App\Actions\Compendium;

use App\Models\Compendium\Compendium;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class GetAllCompendiaForUser
{
    use AsAction;

    public function handle(User $user, array $with = [], array $columns = ['*'])
    {
        $compendiaAllowedToSee = $user->permissions()
                                      ->where('name', 'like', 'compendia.view.%')
                                      ->pluck('name')
                                      ->map(fn($name) => Str::remove('compendia.view.', $name));
        return Compendium::where(function (Builder $query) use ($user, $compendiaAllowedToSee) {
            return $query->where('creator_id', $user->getKey())
                         ->orWhereIn('id', $compendiaAllowedToSee);
        })
                         ->with($with)
                         ->get($columns);
    }

}
