<?php

namespace App\Actions\Item;

use App\Models\Compendium\Character;
use App\Models\Compendium\Species;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteItem
{
    use AsAction;

    public function handle(Species $species): bool
    {
        return $species->delete();
    }
}
