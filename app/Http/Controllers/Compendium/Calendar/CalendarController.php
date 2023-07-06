<?php

namespace App\Http\Controllers\Compendium\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\Calendar\StoreCalendarRequest;
use App\Http\Requests\Compendium\Calendar\UpdateCalendarRequest;
use App\Models\Compendium\Calendar\Calendar;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCalendarRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Calendar $calendar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCalendarRequest $request, Calendar $calendar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calendar $calendar)
    {
        //
    }
}
