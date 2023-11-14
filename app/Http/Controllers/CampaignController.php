<?php

namespace App\Http\Controllers;

use App\Actions\Campaign\GetAllCampaignsForUser;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Campaign::class, 'campaign');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        return CampaignResource::collection(GetAllCampaignsForUser::run(with: $this->with()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampaignRequest $request): CampaignResource
    {
        $params = $request->except('cover_image');
        if ($request->hasFile('cover_image')) {
            $file = Storage::putFile('campaigns', $request->file('cover_image'));
            $params['cover_image'] = $file;
        }
        if (!$request->has('game_master_id')) {
            $params['game_master_id'] = auth()->user()->getKey();
        }

        $campaign = Campaign::create($params);

        // Store the tags associated with the campaign
        if ($request->has('tags')) {
            $tagIds = $request->input('tags');
            $campaign->tags()->sync($tagIds);
        }

        return new CampaignResource($campaign->load($this->with()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign): CampaignResource
    {
        return new CampaignResource($campaign->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignRequest $request, Campaign $campaign): CampaignResource
    {
        $params = $request->except(['cover_image', 'tags']);

        if ($request->hasFile('cover_image')) {
            $file = Storage::putFile('campaigns', $request->file('cover_image'));
            $params['cover_image'] = $file;
        }

        $campaign->update($params);

        if ($request->has('tags')) {
            $tagIds = $request->input('tags');
            $campaign->tags()->sync($tagIds);
        }

        return new CampaignResource($campaign->load($this->with()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign): Response
    {
        $campaign->delete();
        return response()->noContent();
    }
}
