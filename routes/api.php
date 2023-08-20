<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\Compendium\CompendiumController;
use App\Http\Controllers\Compendium\Location;
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

    Route::apiResource('systems', SystemController::class);
    // Campaigns
    Route::apiResource('campaigns', CampaignController::class);
    Route::apiResource('campaigns.sessions', SessionController::class)
        ->shallow();
    // Option sets
    Route::apiResource('location-types', Location\TypeController::class);
    Route::apiResource('government-types', Location\GovernmentTypeController::class);
    // Compendium
    Route::apiResource('compendia', CompendiumController::class);
    Route::apiResource('compendia.locations', Location\LocationController::class)
        ->shallow();
    Route::apiResource('notebooks', NotebookController::class);
    Route::apiResource('notebooks.notes', NoteController::class)
        ->shallow();
});
