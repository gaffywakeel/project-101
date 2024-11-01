<?php

namespace App\Models;

use Akaunting\Money\Money;
use App\CoinAdapters\Contracts\Consolidation;
use App\CoinAdapters\Contracts\NativeAsset;
use App\CoinAdapters\Resources\Transaction;
use App\CoinAdapters\Resources\Wallet as WalletResource;
use App\Helpers\CoinFormatter;
use App\Jobs\ProcessWalletTransaction;
use BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['label', 'passphrase', 'resource'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'coin_id', 'created_at', 'updated_at'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['coin'];

    /**
     * Encrypt passphrase.
     */
    protected function setPassphraseAttribute(string $value): void
    {
        $this->attributes['passphrase'] = encrypt($value);
    }

    /**
     * Decrypt passphrase.
     */
    protected function getPassphraseAttribute(string $value): string
    {
        return decrypt($value);
    }

    /**
     * Consolidates data
     */
    protected function getConsolidatesAttribute(): bool
    {
        return $this->coin->adapter instanceof Consolidation;
    }

    /**
     * Check if adapter has fee address
     */
    protected function getHasFeeAddressAttribute(): bool
    {
        return $this->coin->adapter instanceof NativeAsset;
    }

    /**
     * Get wallet's native
     */
    protected function nativeAsset(): Attribute
    {
        return Attribute::get(function (): ?array {
            if (!$this->coin->adapter instanceof NativeAsset) {
                return null;
            }

            $assetId = $this->coin->adapter->getNativeAssetId();
            $adapter = app('coin.manager')->getAdapter($assetId);

            return [
                'name' => $adapter->getName(),
                'symbol' => $adapter->getSymbol(),
                'icon' => $adapter->getSvgIcon(),
            ];
        });
    }

    /**
     * Get wallet resource
     */
    protected function resource(): Attribute
    {
        return Attribute::make(
            get: function ($value): WalletResource {
                if (Str::isJson($value)) {
                    return new WalletResource(json_decode($value, true));
                } else {
                    return unserialize($value);
                }
            },
            set: fn (WalletResource $resource) => $resource->toJson(),
        );
    }

    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class, 'coin_id', 'id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(WalletAccount::class, 'wallet_id', 'id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id', 'id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(WalletAddress::class, 'wallet_id', 'id');
    }

    /**
     * CommerceFee relation
     */
    public function commerceFee(): HasOne
    {
        return $this->hasOne(CommerceFee::class, 'wallet_id', 'id');
    }

    /**
     * WithdrawalFee relation
     */
    public function withdrawalFee(): HasOne
    {
        return $this->hasOne(WithdrawalFee::class, 'wallet_id', 'id');
    }

    /**
     * ExchangeFee relation
     */
    public function exchangeFees(): HasMany
    {
        return $this->hasMany(ExchangeFee::class, 'wallet_id', 'id');
    }

    /**
     * PeerFee relation
     */
    public function peerFees(): HasMany
    {
        return $this->hasMany(PeerFee::class, 'wallet_id', 'id');
    }

    /**
     * Check if user has wallet account.
     */
    public function hasAccountWithUser(User $user): bool
    {
        return $this->accounts()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if this is email or internal address
     */
    public function hasInternal(string $address): bool
    {
        return filter_var($address, FILTER_VALIDATE_EMAIL) || $this->addresses()->whereAddress($address)->exists();
    }

    /**
     * Get fee address
     */
    public function getFeeAddress(): string
    {
        if (!$this->coin->adapter instanceof NativeAsset) {
            throw new BadMethodCallException(trans('wallet.missing_fee_address'));
        }

        return $this->coin->adapter->getFeeAddress($this->resource);
    }

    /**
     * Get transaction by hash
     */
    public function getTransactionResource(string $hash): Transaction
    {
        return $this->coin->adapter->getTransaction($this->resource, $hash);
    }

    /**
     * Relay transaction
     */
    public function relayTransaction(string $hash): void
    {
        $resource = $this->getTransactionResource($hash);

        ProcessWalletTransaction::dispatch($resource, $this);
    }

    /**
     * Get withdrawal fee
     */
    public function getWithdrawalFee(CoinFormatter $amount): CoinFormatter
    {
        if (!$fee = $this->withdrawalFee) {
            return coin(0, $this->coin, true);
        }

        if ($fee->type != 'fixed') {
            return $amount->multiply(min($fee->value, 99) / 100);
        } else {
            return coin($fee->value, $this->coin, true);
        }
    }

    /**
     * Get exchange fee
     */
    public function getExchangeFee(CoinFormatter $amount, string $category): CoinFormatter
    {
        $query = $this->exchangeFees()->where('category', $category);

        if ($fee = $query->first()) {
            return $amount->multiply(min($fee->value, 99) / 100);
        } else {
            return coin(0, $this->coin, true);
        }
    }

    /**
     * Get peer fee
     */
    public function getPeerFee(CoinFormatter $amount, string $category): CoinFormatter
    {
        $query = $this->peerFees()->where('category', $category);

        if ($fee = $query->first()) {
            return $amount->multiply(min($fee->value, 99) / 100);
        } else {
            return coin(0, $this->coin, true);
        }
    }

    /**
     * Get commerce fee
     */
    public function getCommerceFee(CoinFormatter $amount): CoinFormatter
    {
        if (!$fee = $this->commerceFee) {
            return coin(0, $this->coin, true);
        }

        return $amount->multiply(min($fee->value, 99) / 100);
    }

    /**
     * Get user's wallet account
     */
    public function getAccount(User $user): WalletAccount
    {
        return $this->accounts()
            ->where('user_id', $user->id)
            ->firstOr(function () use ($user) {
                return DB::transaction(function () use ($user) {
                    $account = new WalletAccount();

                    $account->user()->associate($user);
                    $this->accounts()->save($account);

                    $this->createAddress($account);

                    return $account->fresh();
                });
            });
    }

    /**
     * Get Operator account
     */
    public function getOperatorAccount(): ?WalletAccount
    {
        $operator = Module::wallet()->operator;

        return $operator?->getWalletAccount($this);
    }

    /**
     * Create wallet address
     */
    public function createAddress(WalletAccount $account): WalletAddress|false
    {
        $label = $account->user->walletLabel();

        $address = $this->newAddress($label);

        $address->walletAccount()->associate($account);

        return $this->addresses()->save($address);
    }

    /**
     * Initialize new address
     */
    public function newAddress(string $label): WalletAddress
    {
        $address = new WalletAddress();

        $resource = $this->coin->adapter->createAddress($this->resource, $this->passphrase, $label);

        $address->address = $resource->getAddress();
        $address->resource = $resource;

        $address->wallet()->associate($this);

        return $address;
    }

    /**
     * Estimate transaction fee
     */
    public function estimateTransactionFee(string $amount, int $inputs): CoinFormatter
    {
        return coin($this->coin->adapter->estimateTransactionFee($amount, $inputs), $this->coin);
    }

    /**
     * @throws \Exception
     */
    public function send($address, $amount): Transaction
    {
        return $this->coin->adapter->send($this->resource, $address, $amount, $this->passphrase);
    }

    /**
     * Statistic
     */
    public function statistic(): HasOne
    {
        return $this->hasOne(WalletStatistic::class, 'wallet_id', 'id');
    }

    /**
     * Cast amount as Money object
     */
    public function castMoney($amount, string $currency, bool $convertToBase = false): Money
    {
        return new Money($amount, currency($currency, $this->coin->currency_precision), $convertToBase);
    }

    /**
     * Parse amount as money
     */
    public function parseMoney($amount, string $currency): Money
    {
        return $this->castMoney($amount, $currency, true);
    }

    /**
     * Cast value as coin object
     */
    public function castCoin($amount, bool $convertToBase = false): CoinFormatter
    {
        return coin($amount, $this->coin, $convertToBase);
    }

    /**
     * Parse value as coin object
     */
    public function parseCoin($amount): CoinFormatter
    {
        return $this->castCoin($amount, true);
    }

    /**
     * Get unit value
     */
    public function getUnitObject(): CoinFormatter
    {
        return $this->coin->value;
    }

    /**
     * Get dollar price
     */
    public function getDollarPrice(): float
    {
        return $this->coin->getDollarPrice();
    }

    /**
     * Get price in currency
     */
    public function getPrice(string $currency = 'USD', float $dollarPrice = null): float|string
    {
        return $this->coin->value->getPrice($currency, $dollarPrice);
    }

    /**
     * Get price change
     */
    public function getPriceChange(): float
    {
        return $this->coin->getDollarPriceChange();
    }

    /**
     * Get market chart by currency
     */
    public function getMarketChart(string $interval, string $currency): Collection
    {
        return collect($this->coin->getMarketChart($interval))->map(function ($data) use ($currency) {
            [$timestamp, $dollarPrice] = $data;

            $precision = $this->coin->getCurrencyPrecision();

            $converted = app('exchanger')->convert(
                money($dollarPrice, 'USD', true, $precision),
                currency($currency, $precision)
            );

            return [
                'price' => $converted->getValue(),
                'formatted_price' => $converted->format(),
                'timestamp' => $timestamp,
            ];
        });
    }

    /**
     * Scope identifier query
     */
    public function scopeIdentifier(Builder $query, $identifier): Builder
    {
        return $query->whereHas('coin', function (Builder $query) use ($identifier) {
            $query->whereIn('identifier', Arr::wrap($identifier));
        });
    }
}
