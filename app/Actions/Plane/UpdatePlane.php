<?php

namespace App\Actions\Plane;

use App\Models\Compendium\Plane;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdatePlane
{
    use AsAction;

    public function handle(Plane $plane, array $data, array $with = []): Plane
    {
        $plane->update($data);
        return $plane->load($with);
    }
}
