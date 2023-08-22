<?php

namespace App\Actions\Concept;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Concept;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateConceptForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Concept
    {
        return $compendium->concepts()
            ->create($data)
            ->load($with);
    }
}
