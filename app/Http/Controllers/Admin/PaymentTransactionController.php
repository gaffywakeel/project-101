<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentTransactionResource;
use App\Models\PaymentTransaction;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentTransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:payments'));
    }

    /**
     * Paginate transactions
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $query = PaymentTransaction::latest();

        match ($request->query('status')) {
            'completed' => $query->completed(),
            'pending-transfer' => $query->pendingTransfer(),
            'pending-gateway' => $query->pendingGateway(),
            'canceled' => $query->canceled(),
            default => null
        };

        match ($request->query('type')) {
            'receive' => $query->where('type', 'receive'),
            'send' => $query->where('type', 'send'),
            default => null
        };

        $this->filterByUser($query, $request);

        return PaymentTransactionResource::collection($query->autoPaginate());
    }

    /**
     * Filter query by user
     */
    protected function filterByUser(Builder $query, Request $request)
    {
        if ($search = $request->get('searchUser')) {
            $query->whereHas('account.user', function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        }
    }

    /**
     * Complete transfer
     *
     * @return void
     */
    public function completeTransfer(PaymentTransaction $transaction)
    {
        if (!$transaction->isPendingTransfer()) {
            abort(403, trans('auth.action_forbidden'));
        }

        $transaction->completeTransfer();
    }

    /**
     * Cancel transfer
     *
     * @return void
     */
    public function cancelTransfer(PaymentTransaction $transaction)
    {
        if (!$transaction->isPendingTransfer()) {
            abort(403, trans('auth.action_forbidden'));
        }

        $transaction->cancelPending();
    }
}
