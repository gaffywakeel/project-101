<?php

namespace App\Models;

use Akaunting\Money\Money;
use App\Exceptions\CommerceException;
use App\Models\Support\Lock;
use App\Models\Support\Uuid;
use App\Models\Support\ValidationRules;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class CommerceCustomer extends Model
{
    use HasFactory, Uuid, Lock, ValidationRules;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['last_name', 'first_name', 'email'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['last_name', 'first_name', 'email'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['account'];

    /**
     * Create transaction for the user
     */
    public function createTransaction(Money $amount, WalletAccount $walletAccount, Model $subject): CommerceTransaction
    {
        if ($walletAccount->hasMaximumPendingCommerceAddress()) {
            throw new CommerceException(trans('commerce.pending_transaction_limit'));
        }

        return DB::transaction(function () use ($amount, $walletAccount, $subject) {
            $transaction = new CommerceTransaction();

            $currency = $amount->getCurrency()->getCurrency();
            $dollarPrice = $walletAccount->wallet->getDollarPrice();
            $unitPrice = $walletAccount->wallet->getPrice($currency, $dollarPrice);
            $value = $walletAccount->parseCoin($amount->getValue() / $unitPrice);

            $transaction->value = $value;
            $transaction->dollar_price = $dollarPrice;
            $transaction->currency = $currency;

            $walletAddress = $walletAccount->canceledCommerceAddresses()
                ->doesntHave('transferRecords')->firstOr(function () use ($walletAccount) {
                    $label = $this->account->user->walletLabel();

                    return $walletAccount->wallet->newAddress($label);
                });

            $transaction->expires_at = $this->getTransactionExpiryDate();

            $transaction->transactable()->associate($subject);
            $transaction->walletAccount()->associate($walletAccount);
            $transaction->walletAddress()->associate($walletAddress);
            $transaction->account()->associate($this->account);
            $this->transactions()->save($transaction);

            $walletAccount->saveAddress($walletAddress);

            return $transaction;
        });
    }

    /**
     * Active transactions
     */
    public function activeTransactions(Model $subject): HasMany
    {
        return $this->transactions()
            ->whereMorphedTo('transactable', $subject)
            ->latest()->isPending();
    }

    /**
     * Delete only customers without transaction
     */
    public function isDeletable(): bool
    {
        return $this->transactions()->whereNot('status', 'canceled')->doesntExist();
    }

    /**
     * Get transaction expire date
     */
    public function getTransactionExpiryDate(): Carbon
    {
        return now()->addMinutes(settings()->commerce->get('transaction_interval'));
    }

    /**
     * Related transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(CommerceTransaction::class, 'commerce_customer_id', 'id');
    }

    /**
     * Associated business account
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(CommerceAccount::class, 'commerce_account_id', 'id');
    }
}
