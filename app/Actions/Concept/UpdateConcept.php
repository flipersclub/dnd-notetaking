<?php

namespace App\Actions\Concept;

use App\Models\Compendium\Concept;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateConcept
{
    use AsAction;

    public function handle(Concept $concept, array $data, array $with = []): Concept
    {
        $concept->update($data);
        return $concept->load($with);
    }
}
