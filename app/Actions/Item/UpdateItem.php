<?php

namespace App\Actions\Item;

use App\Models\Compendium\Item;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateItem
{
    use AsAction;

    public function handle(Item $item, array $data, array $with = []): Item
    {
        $item->update($data);
        return $item->load($with);
    }
}
