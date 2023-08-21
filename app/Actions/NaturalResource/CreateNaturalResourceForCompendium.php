<?php

namespace App\Actions\NaturalResource;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\NaturalResource;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateNaturalResourceForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): NaturalResource
    {
        return $compendium->naturalResources()
            ->create($data)
            ->load($with);
    }
}
