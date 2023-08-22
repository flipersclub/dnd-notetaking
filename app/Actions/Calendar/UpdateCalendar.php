<?php

namespace App\Actions\Calendar;

use App\Models\Compendium\Calendar\Calendar;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCalendar
{
    use AsAction;

    public function handle(Calendar $calendar, array $data, array $with = []): Calendar
    {
        $calendar->update($data);
        return $calendar->load($with);
    }
}
