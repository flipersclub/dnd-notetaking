<?php

namespace App\Actions\Notebook;

use App\Models\Notebook;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteNotebook
{
    use AsAction;

    public function handle(Notebook $notebook): bool
    {
        return $notebook->delete();
    }
}
