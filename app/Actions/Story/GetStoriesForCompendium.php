<?php

namespace App\Actions\Story;

use App\Models\Compendium\Compendium;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetStoriesForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $with = []): Collection
    {
        return $compendium->stories()
            ->with($with)
            ->get();
    }
}
