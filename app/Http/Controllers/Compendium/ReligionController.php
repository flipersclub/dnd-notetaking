<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Religion\CreateReligionForCompendium;
use App\Actions\Religion\DeleteReligion;
use App\Actions\Religion\GetReligionsForCompendium;
use App\Actions\Religion\UpdateReligion;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreReligionRequest;
use App\Http\Requests\Compendium\UpdateReligionRequest;
use App\Http\Resources\Compendium\ReligionResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Religion;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class ReligionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Religion::class, 'religion');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium): ResourceCollection
    {
        $this->authorize('update', $compendium);
        return ReligionResource::collection(
            GetReligionsForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReligionRequest $request, Compendium $compendium): ReligionResource
    {
        return new ReligionResource(
            CreateReligionForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Religion $religion): ReligionResource
    {
        return new ReligionResource($religion->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReligionRequest $request, Religion $religion): ReligionResource
    {
        return new ReligionResource(
            UpdateReligion::run($religion, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Religion $religion): Response
    {
        DeleteReligion::run($religion);
        return response()->noContent();
    }
}
