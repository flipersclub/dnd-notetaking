<?php

namespace App\Actions\Note;

use App\Models\Note;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteNote
{
    use AsAction;

    public function handle(Note $note): bool
    {
        return $note->delete();
    }
}
