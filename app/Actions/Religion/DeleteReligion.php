<?php

namespace App\Actions\Religion;

use App\Models\Compendium\Religion;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteReligion
{
    use AsAction;

    public function handle(Religion $religion): bool
    {
        return $religion->delete();
    }
}
