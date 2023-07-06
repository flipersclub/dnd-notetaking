<?php

namespace App\Http\Controllers\Compendium\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\Calendar\StoreMonthRequest;
use App\Http\Requests\Compendium\Calendar\UpdateMonthRequest;
use App\Models\Compendium\Calendar\Month;

class MonthController extends Controller
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
    public function store(StoreMonthRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Month $month)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMonthRequest $request, Month $month)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Month $month)
    {
        //
    }
}
