<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEncounterRequest;
use App\Http\Requests\UpdateEncounterRequest;
use App\Models\Encounter;

class EncounterController extends Controller
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
    public function store(StoreEncounterRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Encounter $encounter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEncounterRequest $request, Encounter $encounter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Encounter $encounter)
    {
        //
    }
}
