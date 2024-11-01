<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExchangeSwapResource;
use App\Models\ExchangeSwap;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ExchangeSwapController extends Controller
{
    /**
     * Get paginated ExchangeSwap resources
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', ExchangeSwap::class);

        $query = ExchangeSwap::query()->latest();

        if ($search = $request->query('search_user')) {
            $query->whereHas('sellWalletAccount.user', function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            });
        }

        return ExchangeSwapResource::collection($query->autoPaginate());
    }

    /**
     * Approve ExchangeSwap
     */
    public function approve(ExchangeSwap $exchangeSwap): Response
    {
        $exchangeSwap->acquireLockOrAbort(function (ExchangeSwap $exchangeSwap) {
            $this->authorize('approve', $exchangeSwap);
            $exchangeSwap->approve();
        });

        return response()->noContent();
    }

    /**
     * Cancel ExchangeSwap
     */
    public function cancel(ExchangeSwap $exchangeSwap): Response
    {
        $exchangeSwap->acquireLockOrAbort(function (ExchangeSwap $exchangeSwap) {
            $this->authorize('cancel', $exchangeSwap);
            $exchangeSwap->cancel();
        });

        return response()->noContent();
    }
}
