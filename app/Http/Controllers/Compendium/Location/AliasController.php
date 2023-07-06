<?php

namespace App\Http\Controllers\Compendium\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\Location\StoreAliasRequest;
use App\Http\Requests\Compendium\Location\UpdateAliasRequest;
use App\Models\Compendium\Location\Alias;

class AliasController extends Controller
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
    public function store(StoreAliasRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Alias $locationAlias)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAliasRequest $request, Alias $locationAlias)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alias $locationAlias)
    {
        //
    }
}
