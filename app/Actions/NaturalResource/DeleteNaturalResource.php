<?php

namespace App\Actions\NaturalResource;

use App\Models\Compendium\NaturalResource;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteNaturalResource
{
    use AsAction;

    public function handle(NaturalResource $naturalResource): bool
    {
        return $naturalResource->delete();
    }
}
