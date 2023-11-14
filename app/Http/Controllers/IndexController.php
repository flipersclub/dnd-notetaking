<?php

namespace App\Http\Controllers;

use App\Actions\Compendium\GetAllCompendiaForUser;
use App\Actions\System\CreateSystem;
use App\Actions\System\GetAllSystems;
use App\Actions\System\UpdateSystem;
use App\Enums\ImageType;
use App\Http\Requests\StoreSystemRequest;
use App\Http\Requests\UpdateSystemRequest;
use App\Http\Resources\SystemResource;
use App\Models\Campaign;
use App\Models\Compendium\Compendium;
use App\Models\Image\Image;
use App\Models\Notebook;
use App\Models\System;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class IndexController extends Controller
{
    public function index()
    {
        return [
            'systems' => GetAllSystems::run(columns: ['id', 'slug', 'name']),
            'campaigns' => Campaign::get(['id', 'slug', 'name']), // todo
            'compendia' => GetAllCompendiaForUser::run(user: auth()->user(), with: [
                'locations:id,slug,name,parent_id,compendium_id',
                'characters:id,slug,name,compendium_id',
                'species:id,slug,name,compendium_id',
                'items:id,slug,name,compendium_id',
                'factions:id,slug,name,compendium_id',
                'stories:id,slug,name,compendium_id',
                'concepts:id,slug,name,compendium_id',
                'naturalResources:id,slug,name,compendium_id',
                'currencies:id,slug,name,compendium_id',
                'languages:id,slug,name,compendium_id',
                'religions:id,slug,name,compendium_id',
                'deities:id,slug,name,compendium_id',
                'pantheons:id,slug,name,compendium_id',
                'planes:id,slug,name,compendium_id',
                'encounters:id,slug,name,compendium_id',
                'quests:id,slug,name,compendium_id',
                'spells:id,slug,name,compendium_id',
            ], columns: ['id', 'slug', 'name']),
            'notebooks' => Notebook::where('user_id', auth()->user()->getKey())->with(['notes:id,slug,name,notebook_id'])->get(['id', 'slug', 'name'])
        ];
    }
}
