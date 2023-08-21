<?php

namespace App\Actions\Pantheon;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Pantheon;
use Lorisleiva\Actions\Concerns\AsAction;

class CreatePantheonForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Pantheon
    {
        return $compendium->pantheons()
            ->create($data)
            ->load($with);
    }
}
