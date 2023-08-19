<?php

namespace App\Http\Controllers;

use App\Actions\Notebook\CreateNotebook;
use App\Http\Requests\StoreNotebookRequest;
use App\Http\Requests\UpdateNotebookRequest;
use App\Http\Resources\NotebookResource;
use App\Models\Notebook;

class NotebookController extends Controller
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
    public function store(StoreNotebookRequest $request): NotebookResource
    {
        return new NotebookResource(
            CreateNotebook::make()->handle(
                $request->user(),
                ...$request->validated()
            )->load($this->with())
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Notebook $notebook)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotebookRequest $request, Notebook $notebook)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notebook $notebook)
    {
        //
    }
}
