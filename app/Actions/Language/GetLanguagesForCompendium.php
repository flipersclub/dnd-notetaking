<?php

namespace App\Actions\Language;

use App\Models\Compendium\Compendium;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetLanguagesForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $with = []): Collection
    {
        return $compendium->languages()
            ->with($with)
            ->get();
    }
}
