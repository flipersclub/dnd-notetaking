<?php

namespace App\Actions\Religion;

use App\Models\Compendium\Religion;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateReligion
{
    use AsAction;

    public function handle(Religion $religion, array $data, array $with = []): Religion
    {
        $religion->update($data);
        return $religion->load($with);
    }
}
