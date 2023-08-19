<?php

namespace App\Actions\Note;

use App\Models\Notebook;
use Lorisleiva\Actions\Concerns\AsAction;

class GetNotesInNotebook
{
    use AsAction;

    public function handle(Notebook $notebook, array $with = [])
    {
        return $notebook->notes()
            ->with($with)
            ->get();
    }
}
