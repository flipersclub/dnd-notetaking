<?php

namespace App\Actions\System;

use App\Models\System;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSystem
{
    use AsAction;

    public function handle(System $system, array $data, array $with = []): System
    {
        $system->update($data);
        return $system->load($with);
    }
}
