<?php

namespace App\Casts;

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use App\Models\Support\CurrencyAttribute;
use App\Models\Support\WalletAttribute;
use Closure;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use UnexpectedValueException;

class MoneyCast implements CastsAttributes, SerializesCastableAttributes
{
    /**
     * Determine whether to cast from base unit or not
     */
    protected bool $inBaseUnit;

    /**
     * Create a new cast class instance.
     */
    public function __construct(bool|string $inBaseUnit = true)
    {
        $this->inBaseUnit = is_string($inBaseUnit) ? $inBaseUnit !== 'false' : $inBaseUnit;
    }

    /**
     * Cast the given value.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Money
    {
        if (is_null($value)) {
            return null;
        }

        if (!$model instanceof CurrencyAttribute) {
            throw new InvalidArgumentException('Missing currency attribute.');
        }

        if (!$this->inBaseUnit) {
            $value = floatval($value);
        }

        if ($model instanceof WalletAttribute) {
            return $model->getWallet()->castMoney($value, $model->getCurrency()->getCurrency(), !$this->inBaseUnit);
        } else {
            return new Money($value, $model->getCurrency(), !$this->inBaseUnit);
        }
    }

    /**
     * Prepare the given value for storage.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?float
    {
        if (is_null($value)) {
            return null;
        }

        if (!$value instanceof Money) {
            throw new InvalidArgumentException('Attribute is not a Money object');
        }

        return $this->inBaseUnit ? (float) $value->getAmount() : $value->getValue();
    }

    /**
     * Get the serialized representation of the value.
     */
    public function serialize(Model $model, string $key, mixed $value, array $attributes): ?float
    {
        if (!$value instanceof Money) {
            return $value;
        }

        return $value->getValue();
    }

    /**
     * Assert that attributes has the same base currency
     */
    public static function assert(array|string $attributes): Closure
    {
        $attributes = is_array($attributes) ? $attributes : func_get_args();

        return function (Model $model) use ($attributes) {
            if (!$model instanceof CurrencyAttribute) {
                throw new InvalidArgumentException('Missing currency attribute.');
            }

            collect($attributes)->filter(fn ($name) => $model->isDirty($name))->each(function ($name) use ($model) {
                $attribute = $model->getAttribute($name);

                if (is_null($attribute)) {
                    return null;
                }

                if (!$attribute instanceof Money) {
                    throw new UnexpectedValueException('Attribute is not a Money object');
                }

                if (!$attribute->getCurrency()->equals($model->getCurrency())) {
                    throw new UnexpectedValueException('Different base currency.');
                }
            });
        };
    }
}
