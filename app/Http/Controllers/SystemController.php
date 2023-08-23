<?php

namespace App\Http\Controllers;

use App\Actions\System\CreateSystem;
use App\Actions\System\GetAllSystems;
use App\Actions\System\UpdateSystem;
use App\Enums\ImageType;
use App\Http\Requests\StoreSystemRequest;
use App\Http\Requests\UpdateSystemRequest;
use App\Http\Resources\SystemResource;
use App\Models\Image\Image;
use App\Models\System;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(System::class, 'system');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        return SystemResource::collection(
            GetAllSystems::run($this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSystemRequest $request): SystemResource
    {
        return new SystemResource(
            CreateSystem::run($request->validated(), $this->with())
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(System $system): SystemResource
    {
        return new SystemResource(
            $system->loadMissing($this->with())
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSystemRequest $request, System $system): SystemResource
    {
        return new SystemResource(
            UpdateSystem::run($system, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(System $system): Response
    {
        $system->delete();
        return response()->noContent();
    }
}
