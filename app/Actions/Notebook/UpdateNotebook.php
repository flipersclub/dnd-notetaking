<?php

namespace App\Actions\Notebook;

use App\Models\Notebook;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateNotebook
{
    use AsAction;

    public function handle(Notebook $notebook, array $data, array $with = [])
    {
        $notebook->update($data);
        return $notebook->loadMissing($with);
    }
}
