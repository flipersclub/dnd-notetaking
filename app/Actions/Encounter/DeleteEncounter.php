<?php

namespace App\Actions\Encounter;

use App\Models\Encounter;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteEncounter
{
    use AsAction;

    public function handle(Encounter $encounter): bool
    {
        return $encounter->delete();
    }
}
