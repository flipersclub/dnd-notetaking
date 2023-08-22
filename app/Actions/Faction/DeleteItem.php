<?php

namespace App\Actions\Faction;

use App\Models\Compendium\Faction;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteItem
{
    use AsAction;

    public function handle(Faction $faction): bool
    {
        return $faction->delete();
    }
}
