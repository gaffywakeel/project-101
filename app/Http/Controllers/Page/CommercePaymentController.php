<?php

namespace App\Http\Controllers\Page;

use App\CoinAdapters\Exceptions\AdapterException;
use App\Helpers\LocaleManager;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommerceCustomerResource;
use App\Http\Resources\CommercePaymentResource;
use App\Http\Resources\CommerceTransactionResource;
use App\Models\CommerceAccount;
use App\Models\CommerceCustomer;
use App\Models\CommercePayment;
use App\Models\Module;
use ArrayObject;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CommercePaymentController extends Controller
{
    /**
     * Locale manager
     */
    protected LocaleManager $localeManager;

    /**
     * CommercePaymentController constructor.
     */
    public function __construct(LocaleManager $localeManager)
    {
        $this->localeManager = $localeManager;
    }

    /**
     * Get commerce payment
     */
    public function get(CommercePayment $payment): CommercePaymentResource
    {
        return CommercePaymentResource::make($payment);
    }

    /**
     * Get customer by email
     */
    public function getCustomer(CommercePayment $payment, string $email): CommerceCustomerResource
    {
        $customer = $payment->getCustomerByEmail($email);

        return CommerceCustomerResource::make($customer);
    }

    /**
     * Create customer
     */
    public function createCustomer(Request $request, CommercePayment $payment): CommerceCustomerResource
    {
        $validated = $this->validateCustomerInput($request, $payment->account);

        $customer = $payment->account->customers()->create($validated);

        return CommerceCustomerResource::make($customer);
    }

    /**
     * Get active transaction
     */
    public function getActiveTransaction(CommercePayment $payment, string $email): CommerceTransactionResource
    {
        $customer = $payment->getCustomerByEmail($email);

        $transaction = $customer->activeTransactions($payment)->firstOrFail();

        return CommerceTransactionResource::make($transaction);
    }

    /**
     * Create transaction
     *
     * @return mixed
     */
    public function createTransaction(Request $request, CommercePayment $payment, string $email)
    {
        $validated = $request->validate([
            'coin' => 'required|string|max:10',
        ]);

        $id = $validated['coin'];

        $wallet = $payment->wallets()->identifier($id)->firstOrFail();
        $walletAccount = $wallet->getAccount($payment->account->user);

        return $payment->acquireLockOrAbort(function (CommercePayment $payment) use ($email, $walletAccount) {
            $this->authorize('createTransaction', $payment);

            $customer = $payment->getCustomerByEmail($email);

            return $customer->acquireLockOrAbort(function (CommerceCustomer $customer) use ($payment, $walletAccount) {
                $this->authorize('createTransaction', [$customer, $payment]);

                $transaction = $customer->createTransaction($payment->amount, $walletAccount, $payment);

                return CommerceTransactionResource::make($transaction);
            });
        });
    }

    /**
     * Get payment transaction
     */
    public function getTransaction(CommercePayment $payment, string $id): CommerceTransactionResource
    {
        $transaction = $payment->transactions()->findOrFail($id);

        return CommerceTransactionResource::make($transaction);
    }

    /**
     * Relay transaction
     *
     * @return void
     */
    public function updateTransaction(Request $request, CommercePayment $payment, string $id)
    {
        $validated = $request->validate([
            'hash' => 'required|string|alpha_dash:ascii|max:100',
        ]);

        $hash = $validated['hash'];

        try {
            $transaction = $payment->transactions()->findOrFail($id);

            $transaction->walletAccount->wallet->relayTransaction($hash);
        } catch (AdapterException) {
            abort(422, trans('wallet.failed_transaction_relay'));
        }
    }

    /**
     * Validate customer input
     */
    protected function validateCustomerInput(Request $request, CommerceAccount $account): array
    {
        $uniqueRule = CommerceCustomer::uniqueRule()->where('commerce_account_id', $account->id);

        return $request->validate([
            'email' => ['required', 'email', 'max:100', $uniqueRule],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
        ]);
    }

    /**
     * Get modules
     */
    protected function getModules(): Collection
    {
        return Module::all()->pluck('status', 'name');
    }

    /**
     * Get supported locales object
     */
    protected function getLocales(): ArrayObject
    {
        $locales = $this->localeManager->getLocales();

        return new ArrayObject($locales->toArray());
    }
}
