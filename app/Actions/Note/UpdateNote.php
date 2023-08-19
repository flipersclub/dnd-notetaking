<?php

namespace App\Actions\Note;

use App\Models\Note;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateNote
{
    use AsAction;

    public function handle(Note $note, array $data, array $with = [])
    {
        $note->update($data);
        return $note->load($with);
    }
}
