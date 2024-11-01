<?php

namespace App\Models;

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use App\Casts\MoneyCast;
use App\Models\Support\CurrencyAttribute;
use App\Models\Support\Memoization;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;

class SupportedCurrency extends Model implements CurrencyAttribute
{
    use HasFactory, Memoization;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'min_amount' => MoneyCast::class . ':false',
        'max_amount' => MoneyCast::class . ':false',
        'default' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'exchange_rate',
        'exchange_type',
        'formatted_min_amount',
        'formatted_max_amount',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saving(MoneyCast::assert('min_amount', 'max_amount'));

        static::creating(function (self $record) {
            $record->code = strtoupper($record->code);
        });

        static::deleting(function (self $record) {
            if ($record->default) {
                throw new Exception('Cannot delete default');
            }
        });
    }

    /**
     * Cast amount as Money object
     */
    public function castMoney($amount, bool $convertToBase = false): Money
    {
        return new Money($amount, currency($this->code), $convertToBase);
    }

    /**
     * Parse amount as money
     */
    public function parseMoney($amount): Money
    {
        return $this->castMoney($amount, true);
    }

    /**
     * Scope default query
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->oldest()->where('default', true);
    }

    /**
     * Related payment accounts
     */
    public function paymentAccounts(): HasMany
    {
        return $this->hasMany(PaymentAccount::class, 'currency', 'code');
    }

    /**
     * Statistics
     */
    public function statistic(): HasOne
    {
        return $this->hasOne(SupportedCurrencyStatistic::class, 'supported_currency_code', 'code');
    }

    /**
     * Exchange rate
     */
    protected function getExchangeRate(): ?array
    {
        return $this->memo('exchange_rate', function () {
            return app('exchanger')->getDriver()->find($this->code);
        });
    }

    /**
     * Get formatted_min_amount
     */
    protected function getFormattedMinAmountAttribute(): ?string
    {
        return $this->min_amount?->format();
    }

    /**
     * Get formatted_max_amount
     */
    protected function getFormattedMaxAmountAttribute(): ?string
    {
        return $this->max_amount?->format();
    }

    /**
     * Get exchange rate
     */
    protected function getExchangeRateAttribute(): float|string|null
    {
        return Arr::get($this->getExchangeRate(), 'exchange_rate');
    }

    /**
     * Exchange type, auto|manual
     */
    protected function getExchangeTypeAttribute(): string|null
    {
        return Arr::get($this->getExchangeRate(), 'type');
    }

    /**
     * Get currency symbol
     */
    protected function getSymbolAttribute(): string
    {
        return (new Currency($this->code))->getSymbol();
    }

    /**
     * Find currency by code
     */
    public static function findByCode(?string $code): ?SupportedCurrency
    {
        return static::staticMemo("currency:$code", function () use ($code) {
            return $code ? static::find($code) : null;
        });
    }

    /**
     * Get default currency code
     */
    public static function getDefaultCode(): string
    {
        return static::staticMemo('default_currency', function () {
            return static::default()->first()?->code ?: 'USD';
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrency(): Currency
    {
        return currency($this->code);
    }
}
