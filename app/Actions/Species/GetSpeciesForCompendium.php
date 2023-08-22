<?php

namespace App\Actions\Species;

use App\Models\Compendium\Compendium;
use Lorisleiva\Actions\Concerns\AsAction;

class GetSpeciesForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $with = [])
    {
        return $compendium->species()
            ->with($with)
            ->get();
    }
}
