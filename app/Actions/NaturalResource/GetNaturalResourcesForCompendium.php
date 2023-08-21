<?php

namespace App\Actions\NaturalResource;

use App\Models\Compendium\Compendium;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetNaturalResourcesForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $with = []): Collection
    {
        return $compendium->naturalResources()
            ->with($with)
            ->get();
    }
}
