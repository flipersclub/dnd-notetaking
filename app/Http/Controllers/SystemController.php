<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSystemRequest;
use App\Http\Requests\UpdateSystemRequest;
use App\Http\Resources\SystemResource;
use App\Models\System;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(System::class, 'system');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SystemResource::collection(System::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSystemRequest $request)
    {
        $params = $request->except('cover_image');
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image')->store();
            $params['cover_image'] = $file;
        }
        return new SystemResource(System::create($params));
    }

    /**
     * Display the specified resource.
     */
    public function show(System $system)
    {
        return new SystemResource($system);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSystemRequest $request, System $system)
    {
        $system->update($request->validated());
        return new SystemResource($system);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(System $system)
    {
        $system->delete();
        return response()->noContent();
    }
}
