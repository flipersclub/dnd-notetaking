<?php

namespace App\Actions\Note;

use App\Models\Notebook;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateNoteInNotebook
{
    use AsAction;

    public function handle(Notebook $notebook, array $data, array $with = [])
    {
        return $notebook->notes()
            ->create($data)
            ->load($with);
    }
}
