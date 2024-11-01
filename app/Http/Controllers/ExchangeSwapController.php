<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExchangeSwapRequest;
use App\Http\Resources\ExchangeSwapResource;
use App\Models\ExchangeSwap;
use App\Models\WalletAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExchangeSwapController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('two-factor')->only('store');
    }

    /**
     * Display a listing of the resource.
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $query = $request->user()->exchangeSwaps()->latest();

        return ExchangeSwapResource::collection($query->autoPaginate());
    }

    /**
     * Calculate swap value
     */
    public function calculate(StoreExchangeSwapRequest $request): JsonResponse
    {
        $conversion = $request->conversion();
        $currency = $request->user()->currency;

        return response()->json([
            'sell_value' => $conversion['sell_value']->getValue(),
            'formatted_sell_value_price' => $conversion['sell_value']->getFormattedPrice($currency),
            'sell_value_price' => $conversion['sell_value']->getPrice($currency),
            'buy_value' => $conversion['buy_value']->getValue(),
            'formatted_buy_value_price' => $conversion['buy_value']->getFormattedPrice($currency),
            'buy_value_price' => $conversion['buy_value']->getPrice($currency),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExchangeSwapRequest $request): ExchangeSwapResource
    {
        $conversion = $request->conversion();

        $exchangeSwap = $conversion['sell_account']->acquireLockOrAbort(function (WalletAccount $sellAccount) use ($conversion) {
            if ($sellAccount->getAvailableObject()->lessThan($conversion['sell_value'])) {
                return abort(422, trans('wallet.insufficient_available'));
            }

            $exchangeSwap = new ExchangeSwap();

            $exchangeSwap->sell_value = $conversion['sell_value'];
            $exchangeSwap->sell_dollar_price = $conversion['sell_value']->getDollarPrice();
            $exchangeSwap->sellWalletAccount()->associate($conversion['sell_account']);

            $exchangeSwap->buy_value = $conversion['buy_value'];
            $exchangeSwap->buy_dollar_price = $conversion['buy_value']->getDollarPrice();
            $exchangeSwap->buyWalletAccount()->associate($conversion['buy_account']);

            return tap($exchangeSwap)->save();
        });

        return ExchangeSwapResource::make($exchangeSwap);
    }
}
