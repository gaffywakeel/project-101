<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankAccountResource;
use App\Http\Resources\PeerOfferResource;
use App\Http\Resources\PeerTradeResource;
use App\Models\BankAccount;
use App\Models\PeerOffer;
use App\Models\PeerPaymentMethod;
use App\Models\PeerTrade;
use App\Models\User;
use App\Models\WalletAccount;
use App\Rules\Decimal;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Musonza\Chat\Chat;

class PeerOfferController extends Controller
{
    /**
     * Get offer
     */
    public function get(PeerOffer $offer): PeerOfferResource
    {
        return PeerOfferResource::make($offer);
    }

    /**
     * Create Offer
     *
     * @return mixed
     */
    public function create(Request $request)
    {
        return Auth::user()->acquireLockOrAbort(function (User $user) use ($request) {
            $offer = new PeerOffer();

            $priceMargin = min(settings()->get('price_margin'), 99);

            $account = $this->findAccount($user, $request);
            $offer->walletAccount()->associate($account);

            if (!$user->country) {
                return abort(403, trans('peer.unset_country'));
            }

            $validator = Validator::make($request->all(), [
                'type' => 'required|in:buy,sell',
                'currency' => "required|in:$user->currency",
                'price_type' => 'required|in:fixed,percent',
                'percent_price' => [
                    'required_if:price_type,percent', 'numeric',
                    'min:' . (100 - $priceMargin),
                    'max:' . (100 + $priceMargin),
                ],
                'fixed_price' => [
                    'required_if:price_type,fixed', 'numeric',
                    "min:{$account->min_price_margin->getValue()}",
                    "max:{$account->max_price_margin->getValue()}",
                ],
                'min_amount' => ['required', 'numeric', 'min:1', new Decimal],
                'max_amount' => ['required', 'numeric', 'gt:min_amount', new Decimal],
                'payment' => 'required|in:bank_account,payment_method',
                'time_limit' => 'required|in:15,30,45,60',
                'instruction' => 'required|string|min:50|max:1000',
                'auto_reply' => 'nullable|string|min:50|max:1000',
                'verification' => 'nullable|boolean',
                'long_term' => 'nullable|boolean',
                'following' => 'nullable|boolean',
            ]);

            $validator->sometimes('payment_method', 'required', function ($data) {
                return $data['payment'] === 'payment_method';
            });

            $validator->sometimes('bank_account', 'required', function ($data) {
                return $this->requiresBankAccount($data);
            });

            $validated = $validator->validate();

            $offer->type = $validated['type'];
            $offer->display = $validated['type'] === 'buy';
            $offer->currency = $account->user->currency;
            $offer->country = $account->user->country;
            $offer->price_type = $validated['price_type'];
            $offer->payment = $validated['payment'];

            if ($validated['payment'] === 'payment_method') {
                $offer->paymentMethod()->associate($this->getPaymentMethod($request));
            } elseif ($this->requiresBankAccount($validated)) {
                $offer->bankAccount()->associate($this->getBankAccount($user, $request));
            }

            if ($validated['price_type'] === 'percent') {
                $offer->percent_price = $validated['percent_price'];
            } else {
                $offer->fixed_price = $validated['fixed_price'];
            }

            $offer->min_amount = $account->parseMoney($validated['min_amount']);
            $offer->max_amount = $account->parseMoney($validated['max_amount']);
            $offer->instruction = $validated['instruction'];
            $offer->time_limit = $validated['time_limit'];
            $offer->require_long_term = (bool) data_get($validated, 'long_term');
            $offer->require_verification = (bool) data_get($validated, 'verification');
            $offer->require_following = (bool) data_get($validated, 'following');
            $offer->auto_reply = data_get($validated, 'auto_reply');

            return PeerOfferResource::make(tap($offer)->save());
        });
    }

    /**
     * Start trade
     *
     *
     * @throws AuthorizationException
     */
    public function startTrade(Request $request, PeerOffer $offer): PeerTradeResource
    {
        return Auth::user()->acquireLockOrAbort(function (User $user) use ($request, $offer) {
            return $offer->acquireLockOrAbort(function (PeerOffer $offer) use ($request, $user) {
                $this->authorize('tradable', $offer);

                if ($offer->isSell()) {
                    $sellerWalletAccount = $offer->walletAccount;
                    $buyerWalletAccount = $sellerWalletAccount->parseTarget($user);
                } else {
                    $buyerWalletAccount = $offer->walletAccount;
                    $sellerWalletAccount = $buyerWalletAccount->parseTarget($user);
                }

                return $sellerWalletAccount->acquireLockOrAbort(function (
                    WalletAccount $sellerWalletAccount
                ) use ($buyerWalletAccount, $offer, $request, $user) {
                    $trade = new PeerTrade();

                    $trade->offer()->associate($offer);
                    $trade->sellerWalletAccount()->associate($sellerWalletAccount);
                    $trade->buyerWalletAccount()->associate($buyerWalletAccount);

                    $trade->price = $offer->price;
                    $trade->currency = $offer->currency;
                    $trade->instruction = $offer->instruction;
                    $trade->time_limit = $offer->time_limit;
                    $trade->payment = $offer->payment;

                    $validated = $this->validate($request, [
                        'amount' => [
                            'required', 'numeric',
                            "min:{$offer->min_value->getValue()}",
                            "max:{$offer->max_value->getValue()}",
                        ],
                        'bank_account' => [
                            Rule::requiredIf($offer->requiresBankAccount()),
                        ],
                    ]);

                    $value = $offer->parseCoin($validated['amount']);
                    $feeValue = $offer->getFee($value);
                    $amount = $offer->getPrice($value);

                    if ($offer->payment === 'payment_method') {
                        $trade->paymentMethod()->associate($offer->paymentMethod);
                    } elseif ($offer->requiresBankAccount()) {
                        $bankAccount = $user->bankAccounts()->findOrFail((int) $request->input('bank_account'));
                        $trade->bankAccount()->associate($bankAccount);
                    } else {
                        $trade->bankAccount()->associate($offer->bankAccount);
                    }

                    $totalValue = $offer->isSell() ? $value->add($feeValue) : $value;

                    if ($sellerWalletAccount->getAvailableObject()->lessThan($totalValue)) {
                        return abort(422, trans('peer.insufficient_balance'));
                    }

                    $trade->value = $value;
                    $trade->fee_value = $feeValue;
                    $trade->total_value = $totalValue;
                    $trade->amount = $amount;

                    DB::transaction(function () use ($trade, $sellerWalletAccount, $buyerWalletAccount) {
                        $conversation = app(Chat::class)
                            ->createConversation([
                                $sellerWalletAccount->user,
                                $buyerWalletAccount->user,
                            ])
                            ->makePrivate();

                        $trade->conversation()->associate($conversation);

                        return tap($trade)->save();
                    });

                    return PeerTradeResource::make($trade);
                });
            });
        });
    }

    /**
     * Get BankAccounts
     */
    public function getBankAccounts(PeerOffer $offer): AnonymousResourceCollection
    {
        $accounts = Auth::user()->bankAccounts()->where('currency', $offer->currency)->get();

        return BankAccountResource::collection($accounts);
    }

    /**
     * Enable offer
     *
     * @return void
     *
     * @throws AuthorizationException
     */
    public function enable(PeerOffer $offer)
    {
        $offer->acquireLockOrAbort(function (PeerOffer $offer) {
            $this->authorize('enable', $offer);
            $offer->update(['status' => true]);
        });
    }

    /**
     * Disable offer
     *
     * @return void
     *
     * @throws AuthorizationException
     */
    public function disable(PeerOffer $offer)
    {
        $offer->acquireLockOrAbort(function (PeerOffer $offer) {
            $this->authorize('disable', $offer);
            $offer->update(['status' => false]);
        });
    }

    /**
     * Close peer offer
     *
     * @return void
     *
     * @throws AuthorizationException
     */
    public function close(PeerOffer $offer)
    {
        $offer->acquireLockOrAbort(function (PeerOffer $offer) {
            $this->authorize('close', $offer);
            $offer->update(['closed_at' => now()]);
        });
    }

    /**
     * Paginate Offers
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $type = $request->query('type', 'buy');

        $query = PeerOffer::whereType($type)->displayedFor(Auth::user());

        $this->applyFilters($query, $request);

        return PeerOfferResource::collection($query->autoPaginate());
    }

    /**
     * Apply query filters to offers
     */
    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($wallet = $request->query('wallet')) {
            $query->whereHas('walletAccount.wallet', function (Builder $query) use ($wallet) {
                $query->where('wallets.id', $wallet);
            });
        }

        if ($payment = $request->query('payment')) {
            $query->where('payment', $payment);
        }

        if ($payment === 'payment_method' && $method = $request->query('payment_method')) {
            $query->whereHas('paymentMethod', function (Builder $query) use ($method) {
                $query->where('peer_payment_methods.id', $method);
            });
        }

        if ($country = $request->query('country')) {
            $query->where('country', $country);
        }

        if ($currency = $request->query('currency')) {
            if ($amount = $request->query('amount')) {
                $amount = money($amount, $currency, true);
                $query->where('min_amount', '<=', $amount->getAmount());
                $query->where('max_amount', '>=', $amount->getAmount());
            }

            $query->where('currency', $currency);
        }
    }

    /**
     * Check if bank account is required
     */
    protected function requiresBankAccount($data): bool
    {
        return $data['payment'] == 'bank_account' && $data['type'] == 'sell';
    }

    /**
     * Get user's WalletAccount
     */
    protected function findAccount(User $user, Request $request): WalletAccount
    {
        return $user->walletAccounts()->findOrFail((int) $request->input('account'));
    }

    /**
     * Get user's BankAccount
     */
    protected function getBankAccount(User $user, Request $request): BankAccount
    {
        return $user->activeBankAccounts()->findOrFail((int) $request->input('bank_account'));
    }

    /**
     * Get payment method
     */
    protected function getPaymentMethod(Request $request): PeerPaymentMethod
    {
        return PeerPaymentMethod::findOrFail((int) $request->input('payment_method'));
    }
}
