<?php

namespace App\Actions\Notebook;

use App\Models\Notebook;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class GetNotebooksForUser
{
    use AsAction;

    public function handle(User $user, array $with = [])
    {
        return $user->notebooks()
            ->with($with)
            ->get();
    }
}
