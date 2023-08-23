<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Deity\CreateDeityForCompendium;
use App\Actions\Deity\DeleteDeity;
use App\Actions\Deity\GetDeitiesForCompendium;
use App\Actions\Deity\UpdateDeity;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreDeityRequest;
use App\Http\Requests\Compendium\UpdateDeityRequest;
use App\Http\Resources\Compendium\DeityResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Deity;

class DeityController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Deity::class, 'deity');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium)
    {
        $this->authorize('update', $compendium);
        return DeityResource::collection(
            GetDeitiesForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeityRequest $request, Compendium $compendium)
    {
        return new DeityResource(
            CreateDeityForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Deity $deity)
    {
        return new DeityResource($deity->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeityRequest $request, Deity $deity)
    {
        return new DeityResource(
            UpdateDeity::run($deity, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deity $deity)
    {
        DeleteDeity::run($deity);
        return response()->noContent();
    }
}
