<?php

namespace App\Actions\Plane;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Plane;
use Lorisleiva\Actions\Concerns\AsAction;

class CreatePlaneForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Plane
    {
        return $compendium->planes()
            ->create($data)
            ->load($with);
    }
}
