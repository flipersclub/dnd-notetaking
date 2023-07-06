<?php

namespace App\Http\Controllers\Compendium\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\Calendar\StoreWeekdayRequest;
use App\Http\Requests\Compendium\Calendar\UpdateWeekdayRequest;
use App\Models\Compendium\Calendar\Weekday;

class WeekdayController extends Controller
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
    public function store(StoreWeekdayRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Weekday $weekday)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWeekdayRequest $request, Weekday $weekday)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Weekday $weekday)
    {
        //
    }
}
