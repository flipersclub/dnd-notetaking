<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Plane\CreatePlaneForCompendium;
use App\Actions\Plane\DeletePlane;
use App\Actions\Plane\GetPlanesForCompendium;
use App\Actions\Plane\UpdatePlane;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StorePlaneRequest;
use App\Http\Requests\Compendium\UpdatePlaneRequest;
use App\Http\Resources\Compendium\PlaneResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Plane;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class PlaneController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Plane::class, 'plane');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium): ResourceCollection
    {
        $this->authorize('update', $compendium);
        return PlaneResource::collection(
            GetPlanesForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlaneRequest $request, Compendium $compendium): PlaneResource
    {
        return new PlaneResource(
            CreatePlaneForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Plane $plane): PlaneResource
    {
        return new PlaneResource($plane->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlaneRequest $request, Plane $plane): PlaneResource
    {
        return new PlaneResource(
            UpdatePlane::run($plane, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plane $plane): Response
    {
        DeletePlane::run($plane);
        return response()->noContent();
    }
}
