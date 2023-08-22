<?php

namespace App\Actions\System;

use App\Models\System;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteSystem
{
    use AsAction;

    public function handle(System $system): bool
    {
        return $system->delete();
    }
}
