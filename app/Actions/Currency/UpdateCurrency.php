<?php

namespace App\Actions\Currency;

use App\Models\Compendium\Currency;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCurrency
{
    use AsAction;

    public function handle(Currency $currency, array $data, array $with = []): Currency
    {
        $currency->update($data);
        return $currency->load($with);
    }
}
