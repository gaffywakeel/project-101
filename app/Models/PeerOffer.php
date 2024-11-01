<?php

namespace App\Models;

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use App\Casts\MoneyCast;
use App\Helpers\CoinFormatter;
use App\Models\Support\CurrencyAttribute;
use App\Models\Support\Lock;
use App\Models\Support\Memoization;
use App\Models\Support\Uuid;
use App\Models\Support\WalletAttribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeerOffer extends Model implements WalletAttribute, CurrencyAttribute
{
    use HasFactory, Lock, Uuid, Memoization;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'time_limit',
        'instruction',
        'auto_reply',
        'require_long_term',
        'require_verification',
        'require_following',
        'closed_at',
        'status',
        'display',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'min_amount' => MoneyCast::class,
        'max_amount' => MoneyCast::class,
        'display' => 'boolean',
        'status' => 'boolean',
        'closed_at' => 'datetime',
        'percent_price' => 'float',
        'fixed_price' => 'float',
        'require_long_term' => 'boolean',
        'require_verification' => 'boolean',
        'require_following' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'formatted_min_amount',
        'formatted_max_amount',
        'coin',
        'owner',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['instruction', 'auto_reply'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['walletAccount', 'paymentMethod', 'bankAccount'];

    /**
     * Perform any actions required after the model boots.
     */
    protected static function booted(): void
    {
        static::saving(MoneyCast::assert('min_amount', 'max_amount'));
    }

    /**
     * Parse value as coin object
     */
    public function parseCoin($amount): CoinFormatter
    {
        return $this->walletAccount->wallet->parseCoin($amount);
    }

    /**
     * Parse amount as money
     */
    public function parseMoney($amount): Money
    {
        return $this->walletAccount->wallet->parseMoney($amount, $this->currency);
    }

    /**
     * Get min_amount Object
     */
    public function getMinAmountObject(): Money
    {
        return $this->min_amount;
    }

    /**
     * formatted_min_amount Attribute
     */
    protected function getFormattedMinAmountAttribute(): string
    {
        return $this->getMinAmountObject()->format();
    }

    /**
     * Get max_amount object
     */
    public function getMaxAmountObject(): Money
    {
        return $this->max_amount;
    }

    /**
     * formatted_max_amount Attribute
     */
    protected function getFormattedMaxAmountAttribute(): string
    {
        return $this->getMaxAmountObject()->format();
    }

    /**
     * Get price object
     */
    public function getPriceObject(): Money
    {
        return $this->price;
    }

    /**
     * Get price attribute
     */
    protected function price(): Attribute
    {
        return Attribute::get(function (): Money {
            if ($this->price_type === 'percent') {
                $currentPrice = $this->walletAccount->wallet->getUnitObject()->getPrice($this->currency);

                return $this->parseMoney($currentPrice)->multiply($this->percent_price / 100);
            } else {
                return $this->parseMoney($this->fixed_price);
            }
        });
    }

    /**
     * Get formatted_price attribute
     */
    protected function getFormattedPriceAttribute(): string
    {
        return $this->getPriceObject()->format();
    }

    /**
     * Get price
     */
    public function getPrice(CoinFormatter $amount): Money
    {
        return $this->parseMoney($amount->calcPrice($this->price->getValue()));
    }

    /**
     * Get the min_value object
     */
    public function getMinValueObject(): CoinFormatter
    {
        return $this->min_value;
    }

    /**
     * Get min_value attribute
     */
    protected function minValue(): Attribute
    {
        return Attribute::get(function (): CoinFormatter {
            return $this->parseCoin($this->getMinAmountObject()->getValue() / $this->getPriceObject()->getValue());
        });
    }

    /**
     * Get the max_value object
     */
    public function getMaxValueObject(): CoinFormatter
    {
        return $this->max_value;
    }

    /**
     * Get max_value attribute
     */
    protected function maxValue(): Attribute
    {
        return Attribute::get(function (): CoinFormatter {
            return $this->parseCoin($this->getMaxAmountObject()->getValue() / $this->getPriceObject()->getValue());
        });
    }

    /**
     * Get fee based on offer type
     */
    public function getFee(CoinFormatter $amount): CoinFormatter
    {
        return $this->walletAccount->wallet->getPeerFee($amount, $this->type);
    }

    /**
     * Check if offer can be enabled by user
     */
    public function canEnableBy(User $user): bool
    {
        return $this->isDisabled() && $this->isManagedBy($user);
    }

    /**
     * Check if offer can be disabled by user
     */
    public function canDisableBy(User $user): bool
    {
        return $this->isEnabled() && $this->isManagedBy($user);
    }

    /**
     * Check if offer can be closed by user
     */
    public function canCloseBy(User $user): bool
    {
        return $this->isOpened() && $this->isManagedBy($user);
    }

    /**
     * Check manage ability
     */
    public function isManagedBy(User $user): bool
    {
        return $user->is($this->walletAccount->user) || $user->can('manage:peer_trades');
    }

    /**
     * Check if offer can be traded with user
     */
    public function canTradeWith(User $user): bool
    {
        $status = $this->isAvailable() &&
            $this->owner->isActive() && $this->owner->isNot($user);

        if ($this->require_long_term) {
            $status = $status && $user->isLongTerm();
        }

        if ($this->require_verification) {
            $status = $status && $user->verification->isComplete();
        }

        if ($this->require_following) {
            $status = $status && $user->isFollowing($this->owner);
        }

        return $status;
    }

    /**
     * Check for "sell" offer
     */
    public function isSell(): bool
    {
        return $this->type === 'sell';
    }

    /**
     * Check for "buy" offer
     */
    public function isBuy(): bool
    {
        return $this->type === 'buy';
    }

    /**
     * Check if offer is opened
     */
    public function isOpened(): bool
    {
        return !$this->isClosed();
    }

    /**
     * Check if offer is closed
     */
    public function isClosed(): bool
    {
        return (bool) $this->closed_at;
    }

    /**
     * Displayed check
     */
    public function isDisplayed(): bool
    {
        return (bool) $this->display;
    }

    /**
     * NotDisplayed check
     */
    public function isNotDisplayed(): bool
    {
        return !$this->isDisplayed();
    }

    /**
     * Status check
     */
    public function isEnabled(): bool
    {
        return (bool) $this->status;
    }

    /**
     * Status check
     */
    public function isDisabled(): bool
    {
        return !$this->isEnabled();
    }

    /**
     * Check if offer is available
     */
    public function isAvailable(): bool
    {
        return $this->isOpened() && $this->isEnabled() && $this->isDisplayed();
    }

    /**
     * Check if offer requires bank account.
     */
    public function requiresBankAccount(): bool
    {
        return $this->payment === 'bank_account' && $this->type === 'buy';
    }

    /**
     * Get the creator attribute
     */
    protected function getOwnerAttribute(): User
    {
        return $this->walletAccount->user;
    }

    /**
     * Get coin attribute
     */
    protected function getCoinAttribute(): Coin
    {
        return $this->walletAccount->wallet->coin;
    }

    /**
     * Scope closed query
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->whereNotNull('closed_at');
    }

    /**
     * Scope opened query
     */
    public function scopeOpened(Builder $query): Builder
    {
        return $query->whereNull('closed_at');
    }

    /**
     * Scope displayed query
     */
    public function scopeDisplayed(Builder $query): Builder
    {
        return $query->where('display', true);
    }

    /**
     * Scope notDisplayed query
     */
    public function scopeNotDisplayed(Builder $query): Builder
    {
        return $query->where('display', false);
    }

    /**
     * Scope enabled query
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope disabled query
     */
    public function scopeDisabled(Builder $query): Builder
    {
        return $query->where('status', false);
    }

    /**
     * Scope displayedFor user
     */
    public function scopeDisplayedFor(Builder $query, User $user): Builder
    {
        $query->whereHas('walletAccount.user', function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->whereNull('deactivated_until');
                $query->orWhereDate('deactivated_until', '<', now());
            });
        });

        return $query->opened()->enabled()->displayed();
    }

    /**
     * Scope user ownership
     */
    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->whereHas('walletAccount.user', function (Builder $query) use ($user) {
            $query->where('users.id', $user->id);
        });
    }

    /**
     * Related PeerTrades
     */
    public function trades(): HasMany
    {
        return $this->hasMany(PeerTrade::class, 'offer_id', 'id');
    }

    /**
     * Related wallet account
     */
    public function walletAccount(): BelongsTo
    {
        return $this->belongsTo(WalletAccount::class, 'wallet_account_id', 'id');
    }

    /**
     * Related payment method
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PeerPaymentMethod::class, 'payment_method_id', 'id');
    }

    /**
     * Related bank account
     * (only for "sell" offers)
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id', 'id');
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrency(): Currency
    {
        return currency($this->currency);
    }

    /**
     * {@inheritDoc}
     */
    public function getWallet(): Wallet
    {
        return $this->walletAccount->wallet;
    }
}
