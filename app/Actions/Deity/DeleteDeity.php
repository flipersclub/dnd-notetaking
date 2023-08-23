<?php

namespace App\Actions\Deity;

use App\Models\Compendium\Deity;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteDeity
{
    use AsAction;

    public function handle(Deity $Deity): bool
    {
        return $Deity->delete();
    }
}
