<?php

namespace App\Models;

use Akaunting\Money\Currency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank_name',
        'beneficiary',
        'number',
        'currency',
        'country',
        'note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'bank_logo',
        'currency_name',
        'country_name',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['bank', 'user'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['beneficiary', 'number', 'note', 'user'];

    /**
     * Get related bank
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }

    /**
     * Get bank logo
     */
    protected function getBankLogoAttribute(): ?string
    {
        return $this->bank?->logo;
    }

    /**
     * Get referenced user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Beneficiary name
     */
    protected function getBeneficiaryAttribute($value): ?string
    {
        return !$this->user ? $value : $this->user->profile->full_name;
    }

    /**
     * Bank name
     */
    protected function getBankNameAttribute($value): ?string
    {
        return !$this->bank ? $value : $this->bank->name;
    }

    /**
     * Get currency name
     */
    protected function currencyName(): Attribute
    {
        return Attribute::make(fn () => (new Currency($this->currency))->getName())->shouldCache();
    }

    /**
     * Get country name
     */
    protected function countryName(): Attribute
    {
        return Attribute::make(fn () => config("countries.$this->country"))->shouldCache();
    }

    /**
     * Filter by country.
     */
    public function scopeCountry(Builder $query, string $code): Builder
    {
        return $query->where('country', strtoupper($code));
    }

    /**
     * Filter by currency.
     */
    public function scopeCurrency(Builder $query, string $code): Builder
    {
        return $query->where('currency', strtoupper($code));
    }

    /**
     * Supported currency
     */
    public function supportedCurrency(): BelongsTo
    {
        return $this->belongsTo(SupportedCurrency::class, 'currency', 'code');
    }

    /**
     * Get transfer description
     */
    public function getTransferDescription(): string
    {
        return trans('bank.transfer_description', [
            'bank' => $this->bank_name,
            'number' => $this->number,
        ]);
    }
}
