<?php

namespace App\Http\Controllers;

use App\Http\Resources\OperatingCountryResource;
use App\Http\Resources\SupportedCurrencyResource;
use App\Http\Resources\WalletResource;
use App\Models\OperatingCountry;
use App\Models\SupportedCurrency;
use App\Models\Wallet;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GlobalController extends Controller
{
    /**
     * Get supported currencies.
     */
    public function supportedCurrencies(): AnonymousResourceCollection
    {
        return SupportedCurrencyResource::collection(SupportedCurrency::all());
    }

    /**
     * Get operating countries
     */
    public function operatingCountries(): AnonymousResourceCollection
    {
        return OperatingCountryResource::collection(OperatingCountry::all());
    }

    /**
     * Get array of wallets
     */
    public function wallets(): AnonymousResourceCollection
    {
        return WalletResource::collection(Wallet::all());
    }

    /**
     * Get array of countries
     *
     * @return array
     */
    public function countries()
    {
        return collect(config('countries'))
            ->map(function ($name, $code) {
                return compact('name', 'code');
            })->values()->toArray();
    }
}
