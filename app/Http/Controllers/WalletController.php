<?php

namespace App\Http\Controllers;

use App\CoinAdapters\Exceptions\AdapterException;
use App\Http\Resources\WalletResource;
use App\Models\Wallet;
use BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;

class WalletController extends Controller
{
    /**
     * Get a list of Wallet resources
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Wallet::class);

        $query = Wallet::with('statistic')->withCount('accounts');

        if ($search = $request->get('searchCoin')) {
            $query->whereHas('coin', function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        }

        return WalletResource::collection($query->autoPaginate());
    }

    /**
     * Create Wallet resource
     */
    public function store(Request $request): WalletResource
    {
        $this->authorize('create', Wallet::class);

        return $request->user()->acquireLockOrAbort(function () use ($request) {
            $validated = $request->validate([
                'identifier' => 'required|string|max:10|unique:coins',
                'min_conf' => 'required|integer|min:1|max:99',
            ]);

            $coin = resolve('coin.manager')->register(
                Arr::get($validated, 'identifier'),
                Arr::get($validated, 'min_conf')
            );

            return WalletResource::make($coin->wallet);
        });
    }

    /**
     * Delete Wallet resource
     */
    public function destroy(Wallet $wallet): Response
    {
        $this->authorize('delete', $wallet);

        $wallet->coin->delete();

        return response()->noContent();
    }

    /**
     * Show Wallet fee address
     */
    public function showFeeAddress(Wallet $wallet): JsonResponse
    {
        $this->authorize('view', $wallet);

        try {
            $address = $wallet->getFeeAddress();
        } catch (BadMethodCallException $exception) {
            abort(403, $exception->getMessage());
        }

        return response()->json(['address' => $address]);
    }

    /**
     * Consolidate address funds
     *
     * @throws Throwable
     */
    public function consolidate(Request $request, Wallet $wallet): Response
    {
        $this->authorize('consolidate', $wallet);

        $validated = $request->validate([
            'address' => 'required|string|max:250',
        ]);

        $address = $validated['address'];

        try {
            $wallet->addresses()->findOrFail($address)->consolidate();
        } catch (AdapterException $exception) {
            abort(403, $exception->getMessage());
        }

        return response()->noContent();
    }

    /**
     * Consolidate address funds
     */
    public function relayTransaction(Request $request, Wallet $wallet): Response
    {
        $this->authorize('update', $wallet);

        $validated = $request->validate([
            'hash' => 'required|string|max:250',
        ]);

        $hash = $validated['hash'];

        try {
            $wallet->relayTransaction($hash);
        } catch (AdapterException $exception) {
            abort(403, $exception->getMessage());
        }

        return response()->noContent();
    }

    /**
     * Reset Wallet webhook
     */
    public function resetWebhook(Wallet $wallet): Response
    {
        $this->authorize('update', $wallet);

        $wallet->coin->adapter->resetTransactionWebhook($wallet->resource, $wallet->min_conf);

        return response()->noContent();
    }

    /**
     * Get market chart
     *
     *
     * @throws ValidationException
     */
    public function showMarketChart(Request $request, Wallet $wallet): JsonResponse
    {
        $validated = $request->validate([
            'range' => ['required', 'in:hour,day,week,month,year'],
        ]);

        $range = $validated['range'];
        $currency = $request->user()->currency;

        $chart = $wallet->getMarketChart($range, $currency);

        return response()->json($chart);
    }

    /**
     * Get wallet price
     */
    public function showPrice(Wallet $wallet): JsonResponse
    {
        $unit = $wallet->getUnitObject();
        $currency = Auth::user()->currency;

        return response()->json([
            'price' => $unit->getPrice($currency),
            'formatted_price' => $unit->getFormattedPrice($currency),
            'change' => $wallet->getPriceChange(),
        ]);
    }
}
