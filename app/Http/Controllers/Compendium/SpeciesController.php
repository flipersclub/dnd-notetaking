<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Species\CreateSpeciesForCompendium;
use App\Actions\Species\DeleteSpecies;
use App\Actions\Species\GetSpeciesForCompendium;
use App\Actions\Species\UpdateSpecies;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreSpeciesRequest;
use App\Http\Requests\Compendium\UpdateSpeciesRequest;
use App\Http\Resources\Compendium\SpeciesResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Species;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class SpeciesController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Species::class, 'species');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium): ResourceCollection
    {
        $this->authorize('update', $compendium);
        return SpeciesResource::collection(
            GetSpeciesForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpeciesRequest $request, Compendium $compendium): SpeciesResource
    {
        return new SpeciesResource(
            CreateSpeciesForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Species $species): SpeciesResource
    {
        return new SpeciesResource($species->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpeciesRequest $request, Species $species): SpeciesResource
    {
        return new SpeciesResource(
            UpdateSpecies::run($species, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Species $species): Response
    {
        DeleteSpecies::run($species);
        return response()->noContent();
    }
}
