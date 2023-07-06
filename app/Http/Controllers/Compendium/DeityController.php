<?php

namespace App\Http\Controllers\Compendium;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreDeityRequest;
use App\Http\Requests\Compendium\UpdateDeityRequest;
use App\Models\Compendium\Deity;

class DeityController extends Controller
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
    public function store(StoreDeityRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Deity $deity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeityRequest $request, Deity $deity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deity $deity)
    {
        //
    }
}
