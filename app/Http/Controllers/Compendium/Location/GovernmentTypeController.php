<?php

namespace App\Http\Controllers\Compendium\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\Location\StoreGovernmentTypeRequest;
use App\Http\Requests\Compendium\Location\UpdateGovernmentTypeRequest;
use App\Http\Resources\Compendium\Location\GovernmentTypeResource;
use App\Models\Compendium\Location\GovernmentType;

class GovernmentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return GovernmentTypeResource::collection(GovernmentType::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGovernmentTypeRequest $request)
    {
        // todo: implement
    }

    /**
     * Display the specified resource.
     */
    public function show(GovernmentType $locationGovernmentType)
    {
        // todo: implement
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGovernmentTypeRequest $request, GovernmentType $locationGovernmentType)
    {
        // todo: implement
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GovernmentType $locationGovernmentType)
    {
        // todo: implement
    }
}
