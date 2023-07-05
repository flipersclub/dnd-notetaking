<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpeciesRequest;
use App\Http\Requests\UpdateSpeciesRequest;
use App\Models\Species;

class SpeciesController extends Controller
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
    public function store(StoreSpeciesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Species $monster)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpeciesRequest $request, Species $monster)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Species $monster)
    {
        //
    }
}
