<?php

namespace App\Http\Controllers\Compendium\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\Location\StoreSizeRequest;
use App\Http\Requests\Compendium\Location\UpdateSizeRequest;
use App\Http\Resources\Compendium\Location\SizeResource;
use App\Models\Compendium\Location\Size;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SizeResource::collection(Size::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSizeRequest $request)
    {
        // todo: implement
    }

    /**
     * Display the specified resource.
     */
    public function show(Size $locationSize)
    {
        // todo: implement
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSizeRequest $request, Size $locationSize)
    {
        // todo: implement
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Size $locationSize)
    {
        // todo: implement
    }
}
