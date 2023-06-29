<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpellRequest;
use App\Http\Requests\UpdateSpellRequest;
use App\Models\Spell;

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
     * Show the form for creating a new resource.
     */
    public function create()
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
     * Show the form for editing the specified resource.
     */
    public function edit(Spell $spell)
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
