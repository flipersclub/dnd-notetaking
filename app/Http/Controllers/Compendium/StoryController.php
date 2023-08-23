<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Story\CreateStoryForCompendium;
use App\Actions\Story\DeleteStory;
use App\Actions\Story\GetStoriesForCompendium;
use App\Actions\Story\UpdateStory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreStoryRequest;
use App\Http\Requests\Compendium\UpdateStoryRequest;
use App\Http\Resources\Compendium\StoryResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Story;

class StoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Story::class, 'story');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium)
    {
        $this->authorize('update', $compendium);
        return StoryResource::collection(
            GetStoriesForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStoryRequest $request, Compendium $compendium)
    {
        return new StoryResource(
            CreateStoryForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Story $story)
    {
        return new StoryResource($story->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoryRequest $request, Story $story)
    {
        return new StoryResource(
            UpdateStory::run($story, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Story $story)
    {
        DeleteStory::run($story);
        return response()->noContent();
    }
}
