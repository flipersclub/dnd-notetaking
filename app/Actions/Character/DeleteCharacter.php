<?php

namespace App\Actions\Character;

use App\Models\Compendium\Character;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteCharacter
{
    use AsAction;

    public function handle(Character $character): bool
    {
        return $character->delete();
    }
}
