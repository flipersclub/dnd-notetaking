<?php

namespace App\Http\Controllers\Compendium\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\Location\StoreMapRequest;
use App\Http\Requests\Compendium\Location\UpdateMapRequest;
use App\Models\Compendium\Location\Map;

class MapController extends Controller
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
    public function store(StoreMapRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Map $map)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMapRequest $request, Map $map)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Map $map)
    {
        //
    }
}
