<?php

namespace App\Http\Controllers\Compendium\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\Location\StoreTypeRequest;
use App\Http\Requests\Compendium\Location\UpdateTypeRequest;
use App\Http\Resources\Compendium\Location\TypeResource;
use App\Models\Compendium\Location\Type;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TypeResource::collection(Type::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTypeRequest $request)
    {
        // todo: implement
    }

    /**
     * Display the specified resource.
     */
    public function show(Type $type)
    {
        // todo: implement
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTypeRequest $request, Type $type)
    {
        // todo: implement
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Type $type)
    {
        // todo: implement
    }
}
