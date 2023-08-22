<?php

namespace App\Actions\Species;

use App\Models\Compendium\Compendium;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateSpeciesForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = [])
    {
        return $compendium->species()
            ->create($data)
            ->load($with);
    }
}
