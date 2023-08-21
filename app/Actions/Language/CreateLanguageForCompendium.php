<?php

namespace App\Actions\Language;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Language;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateLanguageForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Language
    {
        return $compendium->languages()
            ->create($data)
            ->load($with);
    }
}
