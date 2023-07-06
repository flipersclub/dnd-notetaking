<?php

namespace App\Http\Controllers\Pivots;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pivots\StoreCharacterLocationRequest;
use App\Http\Requests\Pivots\UpdateCharacterLocationRequest;
use App\Models\Pivots\CharacterLocation;

class CharacterLocationController extends Controller
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
    public function store(StoreCharacterLocationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CharacterLocation $characterLocation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCharacterLocationRequest $request, CharacterLocation $characterLocation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CharacterLocation $characterLocation)
    {
        //
    }
}
