<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Pantheon\CreatePantheonForCompendium;
use App\Actions\Pantheon\DeletePantheon;
use App\Actions\Pantheon\GetPantheonsForCompendium;
use App\Actions\Pantheon\UpdatePantheon;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StorePantheonRequest;
use App\Http\Requests\Compendium\UpdatePantheonRequest;
use App\Http\Resources\Compendium\PantheonResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Pantheon;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class PantheonController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Pantheon::class, 'pantheon');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium): ResourceCollection
    {
        $this->authorize('update', $compendium);
        return PantheonResource::collection(
            GetPantheonsForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePantheonRequest $request, Compendium $compendium): PantheonResource
    {
        return new PantheonResource(
            CreatePantheonForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Pantheon $pantheon): PantheonResource
    {
        return new PantheonResource($pantheon->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePantheonRequest $request, Pantheon $pantheon): PantheonResource
    {
        return new PantheonResource(
            UpdatePantheon::run($pantheon, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pantheon $pantheon): Response
    {
        DeletePantheon::run($pantheon);
        return response()->noContent();
    }
}
