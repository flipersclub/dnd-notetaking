<?php

namespace App\Actions\Character;

use App\Models\Compendium\Compendium;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCharacterForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = [])
    {
        return $compendium->characters()
            ->create($data)
            ->load($with);
    }
}
