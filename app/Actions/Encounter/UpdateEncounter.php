<?php

namespace App\Actions\Encounter;

use App\Models\Encounter;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateEncounter
{
    use AsAction;

    public function handle(Encounter $encounter, array $data, array $with = []): Encounter
    {
        $encounter->update($data);
        return $encounter->load($with);
    }
}
