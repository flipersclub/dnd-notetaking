<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\Compendium\Calendar\CalendarController;
use App\Http\Controllers\Compendium\CharacterController;
use App\Http\Controllers\Compendium\CompendiumController;
use App\Http\Controllers\Compendium\ConceptController;
use App\Http\Controllers\Compendium\CurrencyController;
use App\Http\Controllers\Compendium\DeityController;
use App\Http\Controllers\Compendium\EncounterController;
use App\Http\Controllers\Compendium\FactionController;
use App\Http\Controllers\Compendium\ItemController;
use App\Http\Controllers\Compendium\LanguageController;
use App\Http\Controllers\Compendium\Location;
use App\Http\Controllers\Compendium\NaturalResourceController;
use App\Http\Controllers\Compendium\PantheonController;
use App\Http\Controllers\Compendium\PlaneController;
use App\Http\Controllers\Compendium\QuestController;
use App\Http\Controllers\Compendium\ReligionController;
use App\Http\Controllers\Compendium\SpeciesController;
use App\Http\Controllers\Compendium\SpellController;
use App\Http\Controllers\Compendium\StoryController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\NotebookController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SystemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('index', [IndexController::class, 'index']);

    Route::apiResource('systems', SystemController::class);
    // Campaigns
    Route::apiResource('campaigns', CampaignController::class);
    Route::apiResource('campaigns.sessions', SessionController::class)->shallow();
    // Option sets
    Route::apiResource('location-types', Location\TypeController::class);
    Route::apiResource('government-types', Location\GovernmentTypeController::class);
    // Compendium
    Route::apiResource('compendia', CompendiumController::class);
    Route::apiResource('compendia.locations', Location\LocationController::class)->shallow();
    Route::apiResource('compendia.calendars', CalendarController::class)->shallow();
    Route::apiResource('compendia.characters', CharacterController::class)->shallow();
    Route::apiResource('compendia.concepts', ConceptController::class)->shallow();
    Route::apiResource('compendia.currencies', CurrencyController::class)->shallow();
    Route::apiResource('compendia.deities', DeityController::class)->shallow();
    Route::apiResource('compendia.encounters', EncounterController::class)->shallow();
    Route::apiResource('compendia.factions', FactionController::class)->shallow();
    Route::apiResource('compendia.items', ItemController::class)->shallow();
    Route::apiResource('compendia.languages', LanguageController::class)->shallow();
    Route::apiResource('compendia.natural-resources', NaturalResourceController::class)->shallow();
    Route::apiResource('compendia.pantheons', PantheonController::class)->shallow();
    Route::apiResource('compendia.planes', PlaneController::class)->shallow();
    Route::apiResource('compendia.quests', QuestController::class)->shallow();
    Route::apiResource('compendia.religions', ReligionController::class)->shallow();
    Route::apiResource('compendia.species', SpeciesController::class)->shallow();
    Route::apiResource('compendia.spells', SpellController::class)->shallow();
    Route::apiResource('compendia.stories', StoryController::class)->shallow();
    // Notebooks
    Route::apiResource('notebooks', NotebookController::class);
    Route::apiResource('notebooks.notes', NoteController::class)
        ->shallow();
});
