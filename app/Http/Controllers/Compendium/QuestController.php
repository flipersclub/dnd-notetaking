<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Quest\CreateQuestForCompendium;
use App\Actions\Quest\DeleteQuest;
use App\Actions\Quest\GetQuestsForCompendium;
use App\Actions\Quest\UpdateQuest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreQuestRequest;
use App\Http\Requests\Compendium\UpdateQuestRequest;
use App\Http\Resources\Compendium\QuestResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Quest;

class QuestController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Quest::class, 'quest');
    }
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium)
    {
        $this->authorize('update', $compendium);
        return QuestResource::collection(
            GetQuestsForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuestRequest $request, Compendium $compendium)
    {
        return new QuestResource(
            CreateQuestForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Quest $quest)
    {
        return new QuestResource($quest->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuestRequest $request, Quest $quest)
    {
        return new QuestResource(
            UpdateQuest::run($quest, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quest $quest)
    {
        DeleteQuest::run($quest);
        return response()->noContent();
    }
}
