<?php

namespace App\Actions\NaturalResource;

use App\Models\Compendium\NaturalResource;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateNaturalResource
{
    use AsAction;

    public function handle(NaturalResource $resources, array $data, array $with = []): NaturalResource
    {
        $resources->update($data);
        return $resources->load($with);
    }
}
