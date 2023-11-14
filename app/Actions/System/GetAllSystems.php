<?php

namespace App\Actions\System;

use App\Models\System;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetAllSystems
{
    use AsAction;

    public function handle(array $with = [], array $columns = ['*']): Collection
    {
        return System::with($with)->get($columns);
    }
}
