<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommercePaymentRequest;
use App\Http\Resources\CommercePaymentResource;
use App\Http\Resources\CommerceTransactionResource;
use App\Models\CommerceAccount;
use App\Models\CommercePayment;
use App\Models\Wallet;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CommercePaymentController extends Controller
{
    /**
     * Paginate records
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $query = $this->getAccount()->payments()->latest()
            ->isThroughWeb()->withCount('transactions');

        $this->applyFilter($query, $request);

        return CommercePaymentResource::collection($query->autoPaginate());
    }

    /**
     * Create payment
     */
    public function create(CommercePaymentRequest $request): CommercePaymentResource
    {
        $account = $this->getAccount();

        $payment = new CommercePayment(['source' => 'web']);

        $payment->fill(Arr::except($request->validated(), ['amount', 'expires_at', 'coins']));
        $payment->amount = $payment->supportedCurrency->parseMoney($request->validated('amount'));
        $payment->expires_at = $request->date('expires_at');

        $account->payments()->save($payment);

        $wallets = Wallet::identifier($request->validated('coins'))->pluck('id');
        $payment->wallets()->sync($wallets);

        return CommercePaymentResource::make($payment);
    }

    /**
     * Get payment record
     */
    public function get($id): CommercePaymentResource
    {
        $payment = $this->getAccount()->payments()
            ->withCount('transactions')->findOrFail($id);

        return CommercePaymentResource::make($payment);
    }

    /**
     * Update payment
     *
     *
     * @throws AuthorizationException
     */
    public function update(CommercePaymentRequest $request, $id): CommercePaymentResource
    {
        $payment = $this->getAccount()->payments()->findOrFail($id);

        $this->authorize('update', $payment);

        $attributes = Arr::except($request->validated(), ['expires_at']);
        $payment->expires_at = $request->date('expires_at');

        $payment->update($attributes);

        return CommercePaymentResource::make($payment);
    }

    /**
     * Enable payment
     *
     *
     * @throws AuthorizationException
     */
    public function enable($id): CommercePaymentResource
    {
        $payment = $this->getAccount()->payments()->findOrFail($id);

        $this->authorize('toggleStatus', $payment);

        $payment->update(['status' => true]);

        return CommercePaymentResource::make($payment);
    }

    /**
     * Disable payment
     *
     *
     * @throws AuthorizationException
     */
    public function disable($id): CommercePaymentResource
    {
        $payment = $this->getAccount()->payments()->findOrFail($id);

        $this->authorize('toggleStatus', $payment);

        $payment->update(['status' => false]);

        return CommercePaymentResource::make($payment);
    }

    /**
     * Delete payment
     *
     * @return void
     */
    public function delete($id)
    {
        $payment = $this->getAccount()->payments()->findOrFail($id);

        $payment->acquireLockOrAbort(function (CommercePayment $payment) {
            $this->authorize('delete', $payment);

            $payment->delete();
        });
    }

    /**
     * Paginate payment transactions
     */
    public function transactionPaginate($id): AnonymousResourceCollection
    {
        $query = $this->getAccount()->payments()->findOrFail($id)->transactions()->latest();

        return CommerceTransactionResource::collection($query->autoPaginate());
    }

    /**
     * Apply filter
     */
    protected function applyFilter($query, Request $request): void
    {
        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        if ($currency = $request->query('currency')) {
            $query->where('currency', $currency);
        }

        if ($status = $request->boolean('status')) {
            $query->where('status', $status);
        }

        if ($source = $request->query('source')) {
            $query->where('source', $source);
        }
    }

    /**
     * Get commerce account
     */
    protected function getAccount(): CommerceAccount
    {
        return Auth::user()->commerceAccount()->firstOrFail();
    }
}
