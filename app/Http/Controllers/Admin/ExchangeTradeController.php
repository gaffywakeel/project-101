<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExchangeTradeResource;
use App\Models\ExchangeTrade;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExchangeTradeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:exchange'));
    }

    /**
     * Paginate all exchange trade records
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $query = ExchangeTrade::latest();

        $this->filterByUser($query, $request);

        return ExchangeTradeResource::collection(paginate($query));
    }

    /**
     * Complete pending buy
     *
     * @return mixed
     */
    public function completePending(ExchangeTrade $trade)
    {
        $trade->completePendingBuy();
    }

    /**
     * Cancel pending
     *
     * @return mixed|void
     */
    public function cancelPending(ExchangeTrade $trade)
    {
        $trade->cancelPending();
    }

    /**
     * Filter query by user
     */
    protected function filterByUser(Builder $query, Request $request)
    {
        if ($search = $request->get('searchUser')) {
            $query->whereHas('walletAccount.user', function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        }
    }
}
