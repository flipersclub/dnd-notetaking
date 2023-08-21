<?php

namespace App\Actions\Species;

use App\Models\Compendium\Character;
use App\Models\Compendium\Species;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSpecies
{
    use AsAction;

    public function handle(Species $species, array $data, array $with = [])
    {
        $species->update($data);
        return $species->load($with);
    }
}
