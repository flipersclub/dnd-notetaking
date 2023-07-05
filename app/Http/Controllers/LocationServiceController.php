<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationServiceRequest;
use App\Http\Requests\UpdateLocationServiceRequest;
use App\Models\LocationService;

class LocationServiceController extends Controller
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
    public function store(StoreLocationServiceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LocationService $locationService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationServiceRequest $request, LocationService $locationService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LocationService $locationService)
    {
        //
    }
}
