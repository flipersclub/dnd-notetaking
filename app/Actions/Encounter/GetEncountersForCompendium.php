<?php

namespace App\Actions\Encounter;

use App\Models\Compendium\Compendium;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetEncountersForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $with = []): Collection
    {
        return $compendium->encounters()
            ->with($with)
            ->get();
    }
}
