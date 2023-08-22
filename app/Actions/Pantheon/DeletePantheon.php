<?php

namespace App\Actions\Pantheon;

use App\Models\Compendium\Pantheon;
use Lorisleiva\Actions\Concerns\AsAction;

class DeletePantheon
{
    use AsAction;

    public function handle(Pantheon $pantheon): bool
    {
        return $pantheon->delete();
    }
}
