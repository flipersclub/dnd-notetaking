<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageableTypeRequest;
use App\Http\Requests\UpdateImageableTypeRequest;
use App\Models\Image\Type;

class ImageableTypeController extends Controller
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
    public function store(StoreImageableTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Type $imageableType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImageableTypeRequest $request, Type $imageableType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Type $imageableType)
    {
        //
    }
}
