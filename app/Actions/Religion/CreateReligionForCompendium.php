<?php

namespace App\Actions\Religion;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Religion;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateReligionForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Religion
    {
        return $compendium->religions()
            ->create($data)
            ->load($with);
    }
}
