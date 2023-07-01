<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaneRequest;
use App\Http\Requests\UpdatePlaneRequest;
use App\Models\Plane;

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
