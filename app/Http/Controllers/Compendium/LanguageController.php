<?php

namespace App\Http\Controllers\Compendium;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreLanguageRequest;
use App\Http\Requests\Compendium\UpdateLanguageRequest;
use App\Models\Compendium\Language;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLanguageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLanguageRequest $request, Language $language)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language)
    {
        //
    }
}
