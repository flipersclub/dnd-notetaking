<?php

namespace App\Actions\Notebook;

use App\Models\Notebook;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateNotebook
{
    use AsAction;

    public function handle(User $user, array $data, array $with = [])
    {
        return Notebook::query()
            ->create([
                'user_id' => $user->id,
                ...$data
            ])
            ->load($with);
    }
}
