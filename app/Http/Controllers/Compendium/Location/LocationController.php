<?php

namespace App\Http\Controllers\Compendium\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\Location\StoreLocationRequest;
use App\Http\Requests\Compendium\Location\UpdateLocationRequest;
use App\Http\Resources\Compendium\Location\LocationResource;
use App\Models\Compendium\Location\Location;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Location::class, 'session');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        return LocationResource::collection(Location::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request): LocationResource
    {
        return new LocationResource(Location::create($request->validated())->load($this->with()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location): LocationResource
    {
        return new LocationResource($location);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, Location $location): LocationResource
    {
        $location->update($request->validated());
        return new LocationResource($location);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location): Response
    {
        $location->delete();
        return response()->noContent();
    }
}
