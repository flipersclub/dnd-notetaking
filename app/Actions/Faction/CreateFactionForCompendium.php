<?php

namespace App\Actions\Faction;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Faction;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateFactionForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Faction
    {
        return $compendium->factions()
            ->create($data)
            ->load($with);
    }
}
