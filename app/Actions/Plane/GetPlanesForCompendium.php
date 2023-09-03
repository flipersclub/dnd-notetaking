<?php

namespace App\Actions\Plane;

use App\Models\Compendium\Compendium;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetPlanesForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $with = []): Collection
    {
        return $compendium->planes()
            ->with($with)
            ->get();
    }
}
