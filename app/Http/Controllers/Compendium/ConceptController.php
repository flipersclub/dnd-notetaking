<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Concept\CreateConceptForCompendium;
use App\Actions\Concept\DeleteConcept;
use App\Actions\Concept\GetConceptsForCompendium;
use App\Actions\Concept\UpdateConcept;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreConceptRequest;
use App\Http\Requests\Compendium\UpdateConceptRequest;
use App\Http\Resources\Compendium\ConceptResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Concept;

class ConceptController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Concept::class, 'concept');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium)
    {
        $this->authorize('update', $compendium);
        return ConceptResource::collection(
            GetConceptsForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConceptRequest $request, Compendium $compendium)
    {
        return new ConceptResource(
            CreateConceptForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Concept $concept)
    {
        return new ConceptResource($concept->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConceptRequest $request, Concept $concept)
    {
        return new ConceptResource(
            UpdateConcept::run($concept, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Concept $concept)
    {
        DeleteConcept::run($concept);
        return response()->noContent();
    }
}
