<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaggableRequest;
use App\Http\Requests\UpdateTaggableRequest;
use App\Models\Taggable;

class TaggableController extends Controller
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
    public function store(StoreTaggableRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Taggable $taggable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaggableRequest $request, Taggable $taggable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Taggable $taggable)
    {
        //
    }
}
