<?php

namespace App\Actions\Quest;

use App\Models\Quest;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteQuest
{
    use AsAction;

    public function handle(Quest $quest): bool
    {
        return $quest->delete();
    }
}
