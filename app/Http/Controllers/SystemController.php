<?php

namespace App\Http\Controllers;

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
        return SystemResource::collection(System::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSystemRequest $request): SystemResource
    {
        $system = System::create($request->validated());
        if ($request->hasFile('cover_image')) {
            $file = Storage::putFile("images/{$system->id}", $request->file('cover_image'));
            $image = Image::create(['name' => $file]);
            $system->images()->attach($image, ['type_id' => ImageType::cover->value]);
        }
        return new SystemResource($system->load('coverImage'));
    }

    /**
     * Display the specified resource.
     */
    public function show(System $system): SystemResource
    {
        return new SystemResource($system);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSystemRequest $request, System $system): SystemResource
    {
        $params = $request->except('cover_image');
        if ($request->hasFile('cover_image')) {
            $coverImage = $system->coverImage()->update(['name' => $request->file('cover_image')->getFilename()]);
            Storage::putFile("images/{$coverImage->id}", $request->file('cover_image'));
        }
        $system->update($params);
        return new SystemResource($system);
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
