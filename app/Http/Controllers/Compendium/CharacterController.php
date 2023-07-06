<?php

namespace App\Http\Controllers\Compendium;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreCharacterRequest;
use App\Http\Requests\Compendium\UpdateCharacterRequest;
use App\Models\Compendium\Character;

class CharacterController extends Controller
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
    public function store(StoreCharacterRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Character $character)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCharacterRequest $request, Character $character)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Character $character)
    {
        //
    }
}
