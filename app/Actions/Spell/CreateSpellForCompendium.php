<?php

namespace App\Actions\Spell;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Spell;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateSpellForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Spell
    {
        return $compendium->spells()
            ->create($data)
            ->load($with);
    }
}
