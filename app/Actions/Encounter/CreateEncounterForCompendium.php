<?php

namespace App\Actions\Encounter;

use App\Models\Compendium\Compendium;
use App\Models\Encounter;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateEncounterForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Encounter
    {
        return $compendium->encounters()
            ->create($data)
            ->load($with);
    }
}
