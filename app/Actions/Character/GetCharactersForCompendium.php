<?php

namespace App\Actions\Character;

use App\Models\Compendium\Compendium;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCharactersForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $with = [])
    {
        return $compendium->characters()
            ->with($with)
            ->get();
    }
}
