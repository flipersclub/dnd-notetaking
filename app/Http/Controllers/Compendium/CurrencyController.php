<?php

namespace App\Http\Controllers\Compendium;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreCurrencyRequest;
use App\Http\Requests\Compendium\UpdateCurrencyRequest;
use App\Models\Compendium\Currency;

class CurrencyController extends Controller
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
    public function store(StoreCurrencyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Currency $currency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCurrencyRequest $request, Currency $currency)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        //
    }
}
