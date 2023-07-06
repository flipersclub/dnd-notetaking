<?php

namespace App\Http\Controllers\Compendium\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\Location\StoreGovernmentTypeRequest;
use App\Http\Requests\Compendium\Location\UpdateGovernmentTypeRequest;
use App\Models\Compendium\Location\GovernmentType;

class GovernmentTypeController extends Controller
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
    public function store(StoreGovernmentTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(GovernmentType $locationGovernmentType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGovernmentTypeRequest $request, GovernmentType $locationGovernmentType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GovernmentType $locationGovernmentType)
    {
        //
    }
}
