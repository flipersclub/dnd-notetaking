<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\Compendium\Location;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SettingController;
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
    Route::apiResource('settings', SettingController::class);
    Route::apiResource('campaigns', CampaignController::class);
    Route::apiResource('sessions', SessionController::class);
    Route::apiResource('locations', Location\LocationController::class);
    Route::apiResource('location-types', Location\TypeController::class);
    Route::apiResource('location-sizes', Location\SizeController::class);
    Route::apiResource('government-types', Location\GovernmentTypeController::class);
});
