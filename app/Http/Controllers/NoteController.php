<?php

namespace App\Http\Controllers;

use App\Actions\Note\CreateNoteInNotebook;
use App\Actions\Note\DeleteNote;
use App\Actions\Note\GetNotesInNotebook;
use App\Actions\Note\UpdateNote;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Models\Notebook;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Note::class, 'note');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Notebook $notebook): ResourceCollection
    {
        return NoteResource::collection(
            GetNotesInNotebook::run($notebook, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoteRequest $request, Notebook $notebook): NoteResource
    {
        return new NoteResource(
            CreateNoteInNotebook::run(
                $notebook,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note): NoteResource
    {
        return new NoteResource($note->load($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNoteRequest $request, Note $note): NoteResource
    {
        return new NoteResource(
            UpdateNote::run($note, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note): Response
    {
        DeleteNote::run($note);
        return response()->noContent();
    }
}
