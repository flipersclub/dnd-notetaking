<?php

namespace App\Http\Controllers\Compendium;

use App\Actions\Currency\CreateCurrencyForCompendium;
use App\Actions\Currency\DeleteCurrency;
use App\Actions\Currency\GetCurrenciesForCompendium;
use App\Actions\Currency\UpdateCurrency;
use App\Actions\Deity\CreateDeityForCompendium;
use App\Actions\Deity\DeleteDeity;
use App\Actions\Deity\GetDeitiesForCompendium;
use App\Actions\Deity\UpdateDeity;
use App\Http\Controllers\Controller;
use App\Http\Requests\Compendium\StoreCurrencyRequest;
use App\Http\Requests\Compendium\UpdateCurrencyRequest;
use App\Http\Resources\Compendium\CurrencyResource;
use App\Models\Compendium\Compendium;
use App\Models\Compendium\Currency;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Currency::class, 'currency');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Compendium $compendium)
    {
        $this->authorize('update', $compendium);
        return CurrencyResource::collection(
            GetCurrenciesForCompendium::run($compendium, $this->with())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCurrencyRequest $request, Compendium $compendium)
    {
        return new CurrencyResource(
            CreateCurrencyForCompendium::run(
                $compendium,
                $request->validated(),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Currency $currency)
    {
        return new CurrencyResource($currency->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCurrencyRequest $request, Currency $currency)
    {
        return new CurrencyResource(
            UpdateCurrency::run($currency, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        DeleteCurrency::run($currency);
        return response()->noContent();
    }
}
