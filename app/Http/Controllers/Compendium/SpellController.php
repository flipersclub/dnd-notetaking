<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Spell\CreateSpellForCompendium;
use App\Actions\Spell\DeleteSpell;
use App\Actions\Spell\GetSpellsForCompendium;
use App\Actions\Spell\UpdateSpell;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreSpellRequest;
use App\Http\Requests\Compendium\UpdateSpellRequest;
use App\Http\Resources\Compendium\SpellResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Spell;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class SpellController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Spell::class, 'spell');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium): ResourceCollection
    {
        $this->authorize('update', $compendium);
        return SpellResource::collection(
            GetSpellsForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpellRequest $request, Compendium $compendium): SpellResource
    {
        return new SpellResource(
            CreateSpellForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Spell $spell): SpellResource
    {
        return new SpellResource($spell->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpellRequest $request, Spell $spell): SpellResource
    {
        return new SpellResource(
            UpdateSpell::run($spell, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Spell $spell): Response
    {
        DeleteSpell::run($spell);
        return response()->noContent();
    }
}
