<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Setting::class, 'setting');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        return SettingResource::collection(Setting::with($this->with())->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSettingRequest $request): SettingResource
    {
        $params = $request->except('cover_image');
        if ($request->hasFile('cover_image')) {
            $file = Storage::putFile('settings', $request->file('cover_image'));
            $params['cover_image'] = $file;
        }
        $params['creator_id'] = auth()->user()->getKey();
        return new SettingResource(
            Setting::create($params)->load($this->with())
                ->loadCount('locations')
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting): SettingResource
    {
        return new SettingResource(
            $setting->loadMissing($this->with())
                ->loadCount('locations')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSettingRequest $request, Setting $setting): SettingResource
    {
        $params = $request->except('cover_image');
        if ($request->hasFile('cover_image')) {
            $file = Storage::putFile('settings', $request->file('cover_image'));
            $params['cover_image'] = $file;
        }
        $setting->update($params);
        return new SettingResource(
            $setting->load($this->with())
                ->loadCount('locations')
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting): Response
    {
        $setting->delete();
        return response()->noContent();
    }
}
