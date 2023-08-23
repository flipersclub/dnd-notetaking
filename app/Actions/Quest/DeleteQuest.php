<?php

namespace App\Actions\Quest;

use App\Models\Compendium\Quest;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteQuest
{
    use AsAction;

    public function handle(Quest $quest): bool
    {
        return $quest->delete();
    }
}
