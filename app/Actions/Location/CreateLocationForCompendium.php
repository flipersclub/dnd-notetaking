<?php

namespace App\Actions\Location;

use App\Http\Resources\Compendium\Location\LocationResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Location\Location;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateLocationForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Location
    {
        /** @var Location $location */
        $location = $compendium->locations()
            ->create($data);
        if (isset($data['tags'])) {
            $tagIds = $data['tags'];
            $location->tags()->sync($tagIds);
        }
        return $location->load($with);
    }
}
