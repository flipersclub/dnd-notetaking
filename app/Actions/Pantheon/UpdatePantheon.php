<?php

namespace App\Actions\Pantheon;

use App\Models\Compendium\Pantheon;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdatePantheon
{
    use AsAction;

    public function handle(Pantheon $pantheon, array $data, array $with = []): Pantheon
    {
        $pantheon->update($data);
        return $pantheon->load($with);
    }
}
