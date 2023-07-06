<?php

namespace App\Http\Controllers\Compendium;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StorePantheonRequest;
use App\Http\Requests\Compendium\UpdatePantheonRequest;
use App\Models\Compendium\Pantheon;

class PantheonController extends Controller
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
    public function store(StorePantheonRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Pantheon $pantheon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePantheonRequest $request, Pantheon $pantheon)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pantheon $pantheon)
    {
        //
    }
}
