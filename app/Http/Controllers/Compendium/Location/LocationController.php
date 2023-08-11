<?php

namespace App\Http\Controllers\Compendium\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\Location\StoreLocationRequest;
use App\Http\Requests\Compendium\Location\UpdateLocationRequest;
use App\Http\Resources\Compendium\Location\LocationResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Location\Location;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Location::class, 'location');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium): ResourceCollection
    {
        $this->authorize('update', $compendium);
        return LocationResource::collection($compendium->locations()
            ->with([...$this->with(), 'type'])
            ->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request, Compendium $compendium): LocationResource
    {
        $this->authorize('update', $compendium);
        return new LocationResource(
            Location::create([
                ...$request->validated(),
                'compendium_id' => $compendium->id
            ])->load($this->with())
        );
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
