<?php

namespace App\Models;

use App\Models\Support\ValidationRules;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    use HasFactory, ValidationRules;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Get path for logo
     */
    public function path(): string
    {
        return "banks/{$this->id}";
    }

    /**
     * Get logo url
     */
    protected function getLogoAttribute($value): ?string
    {
        return $value ? url($value) : null;
    }

    /**
     * Bank accounts relation
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(BankAccount::class, 'bank_id', 'id');
    }

    /**
     * Related operating countries
     */
    public function operatingCountries(): BelongsToMany
    {
        return $this->belongsToMany(OperatingCountry::class, 'operating_country_bank', 'bank_id', 'operating_country_code')->withTimestamps();
    }

    /**
     * Filter by country.
     */
    public function scopeCountry(Builder $query, string $code): Builder
    {
        return $query->whereHas('operatingCountries', function (Builder $query) use ($code) {
            $query->where('code', strtoupper($code));
        });
    }
}
