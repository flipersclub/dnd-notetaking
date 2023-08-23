<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Language\CreateLanguageForCompendium;
use App\Actions\Language\DeleteLanguage;
use App\Actions\Language\GetLanguagesForCompendium;
use App\Actions\Language\UpdateLanguage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreLanguageRequest;
use App\Http\Requests\Compendium\UpdateLanguageRequest;
use App\Http\Resources\Compendium\LanguageResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Language;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class LanguageController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Language::class, 'language');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium): ResourceCollection
    {
        $this->authorize('update', $compendium);
        return LanguageResource::collection(
            GetLanguagesForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLanguageRequest $request, Compendium $compendium): LanguageResource
    {
        return new LanguageResource(
            CreateLanguageForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language): LanguageResource
    {
        return new LanguageResource($language->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLanguageRequest $request, Language $language): LanguageResource
    {
        return new LanguageResource(
            UpdateLanguage::run($language, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language): Response
    {
        DeleteLanguage::run($language);
        return response()->noContent();
    }
}
