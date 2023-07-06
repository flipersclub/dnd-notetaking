<?php

namespace App\Http\Controllers\Compendium;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreFactionRequest;
use App\Http\Requests\Compendium\UpdateFactionRequest;
use App\Models\Compendium\Faction;

class FactionController extends Controller
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
    public function store(StoreFactionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Faction $faction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFactionRequest $request, Faction $faction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faction $faction)
    {
        //
    }
}
