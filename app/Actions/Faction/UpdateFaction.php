<?php

namespace App\Actions\Faction;

use App\Models\Compendium\Faction;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateFaction
{
    use AsAction;

    public function handle(Faction $faction, array $data, array $with = []): Faction
    {
        $faction->update($data);
        return $faction->load($with);
    }
}
