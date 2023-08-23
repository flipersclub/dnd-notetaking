<?php

namespace App\Actions\Deity;

use App\Models\Compendium\Deity;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateDeity
{
    use AsAction;

    public function handle(Deity $Deity, array $data, array $with = []): Deity
    {
        $Deity->update($data);
        return $Deity->load($with);
    }
}
