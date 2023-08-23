<?php

namespace App\Actions\Deity;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Deity;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateDeityForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Deity
    {
        return $compendium->deities()
            ->create($data)
            ->load($with);
    }
}
