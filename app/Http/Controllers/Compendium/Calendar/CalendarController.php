<?php

namespace App\Http\Controllers\Compendium\Calendar;

use App\Actions\Calendar\CreateCalendarForCompendium;
use App\Actions\Calendar\DeleteCalendar;
use App\Actions\Calendar\GetCalendarsForCompendium;
use App\Actions\Calendar\UpdateCalendar;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\Calendar\StoreCalendarRequest;
use App\Http\Requests\Compendium\Calendar\UpdateCalendarRequest;
use App\Http\Resources\Calendar\CalendarResource;
use App\Models\Compendium\Calendar\Calendar;
use App\Models\Compendium\Compendium;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Calendar::class, 'calendar');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium)
    {
        $this->authorize('update', $compendium);
        return CalendarResource::collection(
            GetCalendarsForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCalendarRequest $request, Compendium $compendium)
    {
        return new CalendarResource(
            CreateCalendarForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Calendar $calendar)
    {
        return new CalendarResource($calendar->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCalendarRequest $request, Calendar $calendar)
    {
        return new CalendarResource(
            UpdateCalendar::run($calendar, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calendar $calendar)
    {
        DeleteCalendar::run($calendar);
        return response()->noContent();
    }
}
