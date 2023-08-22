<?php

namespace App\Actions\Quest;

use App\Models\Encounter;
use App\Models\Quest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateQuest
{
    use AsAction;

    public function handle(Quest $quest, array $data, array $with = []): Quest
    {
        $quest->update($data);
        return $quest->load($with);
    }
}
