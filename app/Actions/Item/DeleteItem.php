<?php

namespace App\Actions\Item;

use App\Models\Compendium\Item;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteItem
{
    use AsAction;

    public function handle(Item $item): bool
    {
        return $item->delete();
    }
}
