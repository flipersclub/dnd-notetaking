<?php

namespace App\Http\Controllers\Compendium;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreConceptRequest;
use App\Http\Requests\Compendium\UpdateConceptRequest;
use App\Models\Compendium\Concept;

class ConceptController extends Controller
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
    public function store(StoreConceptRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Concept $concept)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConceptRequest $request, Concept $concept)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Concept $concept)
    {
        //
    }
}
