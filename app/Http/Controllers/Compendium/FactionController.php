<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Faction\CreateFactionForCompendium;
use App\Actions\Faction\DeleteFaction;
use App\Actions\Faction\GetFactionsForCompendium;
use App\Actions\Faction\UpdateFaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreFactionRequest;
use App\Http\Requests\Compendium\UpdateFactionRequest;
use App\Http\Resources\Compendium\FactionResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Faction;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class FactionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Faction::class, 'faction');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium): ResourceCollection
    {
        $this->authorize('update', $compendium);
        return FactionResource::collection(
            GetFactionsForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFactionRequest $request, Compendium $compendium): FactionResource
    {
        return new FactionResource(
            CreateFactionForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Faction $faction): FactionResource
    {
        return new FactionResource($faction->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFactionRequest $request, Faction $faction): FactionResource
    {
        return new FactionResource(
            UpdateFaction::run($faction, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faction $faction): Response
    {
        DeleteFaction::run($faction);
        return response()->noContent();
    }
}
