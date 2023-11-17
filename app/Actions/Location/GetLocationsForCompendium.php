<?php

namespace App\Actions\Location;

use App\Models\Compendium\Compendium;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class GetLocationsForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $with = [], ?string $search = null): Collection
    {
        $query = $compendium->locations();
        if (Str::length($search) > 3) {
            $query->where('name', 'like', "%$search%");
        }
        return $query->with($with)
            ->get();
    }
}
