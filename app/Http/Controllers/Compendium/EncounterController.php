<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Encounter\CreateEncounterForCompendium;
use App\Actions\Encounter\DeleteEncounter;
use App\Actions\Encounter\GetEncountersForCompendium;
use App\Actions\Encounter\UpdateEncounter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreEncounterRequest;
use App\Http\Requests\Compendium\UpdateEncounterRequest;
use App\Http\Resources\Compendium\EncounterResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Encounter;

class EncounterController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Encounter::class, 'encounter');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium)
    {
        $this->authorize('update', $compendium);
        return EncounterResource::collection(
            GetEncountersForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEncounterRequest $request, Compendium $compendium)
    {
        return new EncounterResource(
            CreateEncounterForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Encounter $encounter)
    {
        return new EncounterResource($encounter->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEncounterRequest $request, Encounter $encounter)
    {
        return new EncounterResource(
            UpdateEncounter::run($encounter, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Encounter $encounter)
    {
        DeleteEncounter::run($encounter);
        return response()->noContent();
    }
}
