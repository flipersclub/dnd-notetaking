<?php

namespace App\Http\Controllers;

use App\Actions\Session\CreateSessionForCampaign;
use App\Actions\Session\GetSessionsForCampaign;
use App\Actions\Session\UpdateSession;
use App\Http\Requests\StoreSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use App\Http\Resources\SessionResource;
use App\Models\Campaign;
use App\Models\Session;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SessionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Session::class, 'session');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Campaign $campaign): ResourceCollection
    {
        $this->authorize('view', $campaign);
        return SessionResource::collection(
            GetSessionsForCampaign::run($campaign, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSessionRequest $request, Campaign $campaign): SessionResource
    {
        // todo handle cover_image with action
        $params = $request->except('cover_image');
        if ($request->hasFile('cover_image')) {
            $file = Storage::putFile('sessions', $request->file('cover_image'));
            $params['cover_image'] = $file;
        }
        return new SessionResource(
            CreateSessionForCampaign::run(
                $campaign,
                $params,
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Session $session): SessionResource
    {
        return new SessionResource($session->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSessionRequest $request, Session $session): SessionResource
    {
        // todo handle cover_image with action
        $params = $request->except('cover_image');
        if ($request->hasFile('cover_image')) {
            $file = Storage::putFile('sessions', $request->file('cover_image'));
            $params['cover_image'] = $file;
        }
        return new SessionResource(
            UpdateSession::run($session, $params, $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Session $session): Response
    {
        $session->delete();
        return response()->noContent();
    }
}
