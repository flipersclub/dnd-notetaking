<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageableRequest;
use App\Http\Requests\UpdateImageableRequest;
use App\Models\Image\Imageable;

class ImageableController extends Controller
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
    public function store(StoreImageableRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Imageable $imageable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImageableRequest $request, Imageable $imageable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Imageable $imageable)
    {
        //
    }
}
