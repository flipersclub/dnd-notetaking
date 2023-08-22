<?php

namespace App\Actions\Currency;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Currency;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCurrencyForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Currency
    {
        return $compendium->currencies()
            ->create($data)
            ->load($with);
    }
}
