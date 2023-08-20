<?php

namespace App\Actions\Character;

use App\Models\Compendium\Character;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCharacter
{
    use AsAction;

    public function handle(Character $character, array $data, array $with = [])
    {
        $character->update($data);
        return $character->load($with);
    }
}
