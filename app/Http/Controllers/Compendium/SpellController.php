<?php

namespace App\Http\Controllers\Compendium;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreSpellRequest;
use App\Http\Requests\Compendium\UpdateSpellRequest;
use App\Models\Compendium\Spell;

class SpellController extends Controller
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
    public function store(StoreSpellRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Spell $spell)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpellRequest $request, Spell $spell)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Spell $spell)
    {
        //
    }
}
