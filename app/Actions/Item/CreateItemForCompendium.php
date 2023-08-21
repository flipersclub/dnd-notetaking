<?php

namespace App\Actions\Item;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Item;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateItemForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Item
    {
        return $compendium->items()
            ->create($data)
            ->load($with);
    }
}
