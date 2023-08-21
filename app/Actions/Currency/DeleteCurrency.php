<?php

namespace App\Actions\Currency;

use App\Models\Compendium\Currency;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteCurrency
{
    use AsAction;

    public function handle(Currency $currency): bool
    {
        return $currency->delete();
    }
}
