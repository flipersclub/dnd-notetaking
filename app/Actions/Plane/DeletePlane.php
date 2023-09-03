<?php

namespace App\Actions\Plane;

use App\Models\Compendium\Plane;
use Lorisleiva\Actions\Concerns\AsAction;

class DeletePlane
{
    use AsAction;

    public function handle(Plane $plane): bool
    {
        return $plane->delete();
    }
}
