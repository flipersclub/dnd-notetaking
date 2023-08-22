<?php

namespace App\Actions\Species;

use App\Models\Compendium\Character;
use App\Models\Compendium\Species;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteSpecies
{
    use AsAction;

    public function handle(Species $species): bool
    {
        return $species->delete();
    }
}
