<?php

namespace App\Http\Controllers\Compendium;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StorePlaneRequest;
use App\Http\Requests\Compendium\UpdatePlaneRequest;
use App\Models\Compendium\Plane;

class PlaneController extends Controller
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
    public function store(StorePlaneRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Plane $plane)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlaneRequest $request, Plane $plane)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plane $plane)
    {
        //
    }
}
