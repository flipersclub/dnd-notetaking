<?php

namespace App\Actions\Story;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Story;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateStoryForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Story
    {
        return $compendium->stories()
            ->create($data)
            ->load($with);
    }
}
