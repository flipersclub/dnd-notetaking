<?php

namespace App\Actions\System;

use App\Models\System;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateSystem
{
    use AsAction;

    public function handle(array $data, array $with = []): System
    {
        return System::create($data)
            ->load($with);
    }
}
