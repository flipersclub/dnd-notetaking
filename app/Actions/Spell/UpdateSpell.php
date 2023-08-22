<?php

namespace App\Actions\Spell;

use App\Models\Compendium\Spell;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSpell
{
    use AsAction;

    public function handle(Spell $spell, array $data, array $with = []): Spell
    {
        $spell->update($data);
        return $spell->load($with);
    }
}
