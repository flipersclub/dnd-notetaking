<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use App\Http\Resources\SessionResource;
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
    public function index(): ResourceCollection
    {
        return SessionResource::collection(Session::with($this->with())->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSessionRequest $request): SessionResource
    {
        $params = $request->except('cover_image');
        if ($request->hasFile('cover_image')) {
            $file = Storage::putFile('sessions', $request->file('cover_image'));
            $params['cover_image'] = $file;
        }
        $params['creator_id'] = auth()->user()->getKey();
        return new SessionResource(Session::create($params)->load($this->with()));
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
        $params = $request->except('cover_image');
        if ($request->hasFile('cover_image')) {
            $file = Storage::putFile('sessions', $request->file('cover_image'));
            $params['cover_image'] = $file;
        }
        $session->update($params);
        return new SessionResource($session->load($this->with()));
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
