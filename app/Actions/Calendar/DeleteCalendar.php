<?php

namespace App\Actions\Calendar;

use App\Models\Compendium\Calendar;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteCalendar
{
    use AsAction;

    public function handle(Calendar $calendar): bool
    {
        return $calendar->delete();
    }
}
