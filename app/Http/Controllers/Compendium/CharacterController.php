<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Character\CreateCharacterForCompendium;
use App\Actions\Character\DeleteCharacter;
use App\Actions\Character\GetCharactersForCompendium;
use App\Actions\Character\UpdateCharacter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreCharacterRequest;
use App\Http\Requests\Compendium\UpdateCharacterRequest;
use App\Http\Resources\Compendium\CharacterResource;
use App\Models\Compendium\Character;
use App\Models\Compendium\Compendium;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CharacterController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Character::class, 'character');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium): ResourceCollection
    {
        $this->authorize('update', $compendium);
        return CharacterResource::collection(
            GetCharactersForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCharacterRequest $request, Compendium $compendium)
    {
        return new CharacterResource(
            CreateCharacterForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Character $character)
    {
        return new CharacterResource($character->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCharacterRequest $request, Character $character)
    {
        return new CharacterResource(
            UpdateCharacter::run($character, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Character $character)
    {
        DeleteCharacter::run($character);
        return response()->noContent();
    }
}
