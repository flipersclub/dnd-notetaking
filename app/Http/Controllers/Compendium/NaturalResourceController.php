<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\NaturalResource\CreateNaturalResourceForCompendium;
use App\Actions\NaturalResource\DeleteNaturalResource;
use App\Actions\NaturalResource\GetNaturalResourcesForCompendium;
use App\Actions\NaturalResource\UpdateNaturalResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreNaturalResourceRequest;
use App\Http\Requests\Compendium\UpdateNaturalResourceRequest;
use App\Http\Resources\Compendium\NaturalResourceResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\NaturalResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class NaturalResourceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(NaturalResource::class, 'natural_resource');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium): ResourceCollection
    {
        $this->authorize('update', $compendium);
        return NaturalResourceResource::collection(
            GetNaturalResourcesForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNaturalResourceRequest $request, Compendium $compendium): NaturalResourceResource
    {
        return new NaturalResourceResource(
            CreateNaturalResourceForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(NaturalResource $naturalResource): NaturalResourceResource
    {
        return new NaturalResourceResource($naturalResource->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNaturalResourceRequest $request, NaturalResource $naturalResource): NaturalResourceResource
    {
        return new NaturalResourceResource(
            UpdateNaturalResource::run($naturalResource, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NaturalResource $naturalResource): Response
    {
        DeleteNaturalResource::run($naturalResource);
        return response()->noContent();
    }
}
