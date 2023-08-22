<?php

namespace App\Actions\Spell;

use App\Models\Compendium\Spell;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteSpell
{
    use AsAction;

    public function handle(Spell $spell): bool
    {
        return $spell->delete();
    }
}
