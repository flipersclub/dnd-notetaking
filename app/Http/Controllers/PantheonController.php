<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePantheonRequest;
use App\Http\Requests\UpdatePantheonRequest;
use App\Models\Pantheon;

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
