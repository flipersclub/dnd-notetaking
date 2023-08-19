<?php

namespace App\Http\Controllers;

use App\Actions\Notebook\CreateNotebook;
use App\Actions\Notebook\DeleteNotebook;
use App\Actions\Notebook\GetNotebooksForUser;
use App\Actions\Notebook\UpdateNotebook;
use App\Http\Requests\StoreNotebookRequest;
use App\Http\Requests\UpdateNotebookRequest;
use App\Http\Resources\NotebookResource;
use App\Models\Notebook;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NotebookController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Notebook::class, 'notebook');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        return NotebookResource::collection(
            GetNotebooksForUser::run(auth()->user(), $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotebookRequest $request): NotebookResource
    {
        return new NotebookResource(
            CreateNotebook::run($request->user(), $request->validated(), $this->with())
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Notebook $notebook): NotebookResource
    {
        return new NotebookResource($notebook->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotebookRequest $request, Notebook $notebook): NotebookResource
    {
        return new NotebookResource(
            UpdateNotebook::make()->handle($notebook, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notebook $notebook)
    {
        DeleteNotebook::run($notebook);
        return response()->noContent();
    }
}
