<?php

namespace App\Actions\Concept;

use App\Models\Compendium\Concept;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteConcept
{
    use AsAction;

    public function handle(Concept $concept): bool
    {
        return $concept->delete();
    }
}
