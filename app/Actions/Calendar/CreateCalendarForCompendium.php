<?php

namespace App\Actions\Calendar;

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Calendar\Calendar;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCalendarForCompendium
{
    use AsAction;

    public function handle(Compendium $compendium, array $data, array $with = []): Calendar
    {
        return $compendium->calendars()
            ->create($data)
            ->load($with);
    }
}
