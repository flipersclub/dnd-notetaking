<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Compendium\GetAllCompendiaForUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompendiumRequest;
use App\Http\Requests\UpdateCompendiumRequest;
use App\Http\Resources\CompendiumResource;
use App\Models\Compendium\Compendium;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CompendiumController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Compendium::class, 'compendium');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        return CompendiumResource::collection(
            GetAllCompendiaForUser::run(auth()->user(), $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompendiumRequest $request): CompendiumResource
    {
        $params = $request->except('cover_image');
        if ($request->hasFile('cover_image')) {
            $file = Storage::putFile('compendia', $request->file('cover_image'));
            $params['cover_image'] = $file;
        }
        $params['creator_id'] = auth()->user()->getKey();
        return new CompendiumResource(
            Compendium::create($params)->load($this->with())
                ->loadCount(['locations', 'characters'])
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Compendium $compendium): CompendiumResource
    {
        return new CompendiumResource(
            $compendium->loadMissing($this->with())
                ->loadCount(['locations', 'characters'])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompendiumRequest $request, Compendium $compendium): CompendiumResource
    {
        $params = $request->except('cover_image');
        if ($request->hasFile('cover_image')) {
            $file = Storage::putFile('compendia', $request->file('cover_image'));
            $params['cover_image'] = $file;
        }
        $compendium->update($params);
        return new CompendiumResource(
            $compendium->load($this->with())
                ->loadCount(['locations', 'characters'])
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Compendium $compendium): Response
    {
        $compendium->delete();
        return response()->noContent();
    }
}
