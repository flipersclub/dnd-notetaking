<?php

namespace App\Actions\Quest;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Quest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateQuestForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Quest
    {
        return $compendium->quests()
            ->create($data)
            ->load($with);
    }
}
