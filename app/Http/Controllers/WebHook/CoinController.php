<?php

namespace App\Http\Controllers\WebHook;

use App\CoinAdapters\Resources\Transaction;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessWalletTransaction;
use App\Models\Coin;
use Illuminate\Http\Request;

class CoinController extends Controller
{
    /**
     * Handle transaction webhook for coin
     *
     * @return void
     *
     * @throws \Exception
     */
    public function handleTransaction(Request $request, $identifier)
    {
        if ($coin = $this->getCoinByIdentifier($identifier)) {
            $resource = $coin->adapter->handleTransactionWebhook($coin->wallet->resource, $request->all());

            if ($resource instanceof Transaction) {
                ProcessWalletTransaction::dispatch($resource, $coin->wallet);
            }
        }
    }

    /**
     * Get coin model
     */
    protected function getCoinByIdentifier($identifier): ?Coin
    {
        return Coin::where('identifier', $identifier)->first();
    }
}
