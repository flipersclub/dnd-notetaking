<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Item\CreateItemForCompendium;
use App\Actions\Item\DeleteItem;
use App\Actions\Item\GetItemsForCompendium;
use App\Actions\Item\UpdateItem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreItemRequest;
use App\Http\Requests\Compendium\UpdateItemRequest;
use App\Http\Resources\Compendium\ItemResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Item;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Item::class, 'item');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium): ResourceCollection
    {
        $this->authorize('update', $compendium);
        return ItemResource::collection(
            GetItemsForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request, Compendium $compendium): ItemResource
    {
        return new ItemResource(
            CreateItemForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item): ItemResource
    {
        return new ItemResource($item->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item): ItemResource
    {
        return new ItemResource(
            UpdateItem::run($item, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item): Response
    {
        DeleteItem::run($item);
        return response()->noContent();
    }
}
