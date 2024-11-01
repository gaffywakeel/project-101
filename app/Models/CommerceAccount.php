<?php

namespace App\Models;

use App\Casts\PurifyHtml;
use App\Models\Support\ValidationRules;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CommerceAccount extends Model
{
    use HasFactory, ValidationRules;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['name', 'email', 'website', 'phone', 'about'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['user'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'about' => PurifyHtml::class,
    ];

    /**
     * Get logo path
     */
    public function path(): string
    {
        return "commerce-accounts/{$this->id}";
    }

    /**
     * Get logo url
     */
    public function getLogoAttribute($value): ?string
    {
        return $value ? url($value) : null;
    }

    /**
     * Get transaction aggregate
     */
    public function getTransactionAggregate(?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $query = $this->transactions();

        if ($from instanceof Carbon) {
            $query->where('created_at', '>=', $from->toDateTimeString());
        }

        if ($to instanceof Carbon) {
            $query->where('created_at', '<=', $to->toDateTimeString());
        }

        $query->selectRaw('count(*) as total');
        $query->selectRaw('sum(value) as total_value');
        $query->selectRaw('wallet_account_id, status');
        $query->groupBy('wallet_account_id', 'status');

        return $query->get()->map(function ($result) {
            $walletAccount = $this->getWalletAccount($result->wallet_account_id);
            $totalValue = $walletAccount->wallet->castCoin($result->total_value);
            $totalPrice = $totalValue->getPriceAsMoney($this->user->currency);

            return [
                'status' => $result->status,
                'coin' => $walletAccount->wallet->coin->identifier,
                'total_value' => $totalValue->getValue(),
                'total_price' => $totalPrice->getValue(),
                'formatted_total_price' => $totalPrice->format(),
                'total' => $result->total,
            ];
        });
    }

    /**
     * Aggregate transaction status
     */
    public function getTransactionStatusAggregate(string $status, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $query = $this->transactions()->whereStatus($status);

        if ($from instanceof Carbon) {
            $query->where('created_at', '>=', $from->toDateTimeString());
        }

        if ($to instanceof Carbon) {
            $query->where('created_at', '<=', $to->toDateTimeString());
        }

        $query->selectRaw('count(*) as total');
        $query->selectRaw('sum(value) as total_value');
        $query->selectRaw('wallet_account_id');
        $query->groupBy('wallet_account_id');

        return $query->get()->map(function ($result) {
            $walletAccount = $this->getWalletAccount($result->wallet_account_id);
            $totalValue = $walletAccount->wallet->castCoin($result->total_value);
            $totalPrice = $totalValue->getPriceAsMoney($this->user->currency);

            return [
                'coin' => $walletAccount->wallet->coin->identifier,
                'total_value' => $totalValue->getValue(),
                'total_price' => $totalPrice->getValue(),
                'formatted_total_price' => $totalPrice->format(),
                'total' => $result->total,
            ];
        });
    }

    /**
     * Get total customers
     */
    public function getCustomerCount(?Carbon $from = null, ?Carbon $to = null): int
    {
        $query = $this->customers();

        if ($from instanceof Carbon) {
            $query->where('created_at', '>=', $from->toDateTimeString());
        }

        if ($to instanceof Carbon) {
            $query->where('created_at', '<=', $to->toDateTimeString());
        }

        return $query->count();
    }

    /**
     * Find related wallet account
     */
    public function getWalletAccount(int $id): WalletAccount
    {
        return $this->user->walletAccounts()->findOrFail($id);
    }

    /**
     * Get transaction interval
     */
    public function transactionInterval(): Attribute
    {
        return Attribute::get(fn () => settings()->commerce->get('transaction_interval'));
    }

    /**
     * Business owner
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Related customers
     */
    public function customers(): HasMany
    {
        return $this->hasMany(CommerceCustomer::class, 'commerce_account_id', 'id');
    }

    /**
     * Related payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(CommercePayment::class, 'commerce_account_id', 'id');
    }

    /**
     * Related Transaction
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(CommerceTransaction::class, 'commerce_account_id', 'id');
    }
}
