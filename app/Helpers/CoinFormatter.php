<?php

namespace App\Helpers;

use Akaunting\Money\Money;
use App\Models\Coin;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use InvalidArgumentException;

class CoinFormatter
{
    /**
     * Coin model
     */
    protected Coin $coin;

    /**
     * Base amount
     */
    protected BigDecimal $amount;

    /**
     * Coin value
     */
    protected float $value;

    /**
     * Rounding mode
     */
    protected int $rounding = RoundingMode::HALF_DOWN;

    /**
     * Coin constructor.
     */
    public function __construct($amount, Coin $coin, bool $convertToBase = false)
    {
        $this->coin = $coin;
        $this->amount = $this->parseAmount($amount, $convertToBase);
    }

    /**
     * Parse amount.
     */
    protected function parseAmount($amount, bool $convertToBase = false): BigDecimal
    {
        return $this->convertToBase(BigDecimal::of($amount), $convertToBase)->toScale(0, $this->rounding);
    }

    /**
     * Convert amount to base unit.
     */
    protected function convertToBase(BigDecimal $amount, bool $convertToBase = false): BigDecimal
    {
        if (!$convertToBase) {
            return $amount;
        }

        return $amount->multipliedBy($this->coin->getBaseUnit());
    }

    /**
     * Less than comparison
     */
    public function lessThan(self $that): bool
    {
        $this->assertSameCoin($that);

        return $this->amount->isLessThan($that->getAmount());
    }

    /**
     * Less than or equal comparison
     */
    public function lessThanOrEqual(self $that): bool
    {
        $this->assertSameCoin($that);

        return $this->amount->isLessThanOrEqualTo($that->getAmount());
    }

    /**
     * Greater than comparison
     */
    public function greaterThan(self $that): bool
    {
        $this->assertSameCoin($that);

        return $this->amount->isGreaterThan($that->getAmount());
    }

    /**
     * Greater than or equal comparison
     */
    public function greaterThanOrEqual(self $that): bool
    {
        $this->assertSameCoin($that);

        return $this->amount->isGreaterThanOrEqualTo($that->getAmount());
    }

    /**
     * Check for zero value
     */
    public function isZero(): bool
    {
        return $this->amount->isZero();
    }

    /**
     * Check for negative or zero value
     */
    public function isNegativeOrZero(): bool
    {
        return $this->amount->isNegativeOrZero();
    }

    /**
     * Check for negative value
     */
    public function isNegative(): bool
    {
        return $this->amount->isNegative();
    }

    /**
     * Check for positive value
     */
    public function isPositive(): bool
    {
        return $this->amount->isPositive();
    }

    /**
     * Add operation
     */
    public function add(self $that): CoinFormatter
    {
        $this->assertSameCoin($that);

        return new self($this->amount->plus($that->getAmount()), $this->coin);
    }

    /**
     * Subtract operation
     */
    public function subtract(self $that): CoinFormatter
    {
        $this->assertSameCoin($that);

        return new self($this->amount->minus($that->getAmount()), $this->coin);
    }

    /**
     * Multiply operation
     */
    public function multiply(float $multiplier): CoinFormatter
    {
        return new self($this->amount->multipliedBy($multiplier), $this->coin);
    }

    /**
     * Assert that operation is done on the same Coin object
     */
    protected function assertSameCoin(self $that): void
    {
        if ($this->coin->isNot($that->getCoin())) {
            throw new InvalidArgumentException('Different base coin');
        }
    }

    /**
     * Get Coin Model
     */
    public function getCoin(): Coin
    {
        return $this->coin;
    }

    /**
     * Get amount in Base Unit.
     */
    public function getAmount(): string
    {
        return (string) $this->amount;
    }

    /**
     * Get value as float.
     */
    public function getValue(): float
    {
        if (!isset($this->value)) {
            $this->value = $this->amount->exactlyDividedBy($this->coin->getBaseUnit())
                ->toScale($this->coin->getPrecision(), $this->rounding)->toFloat();
        }

        return $this->value;
    }

    /**
     * Get underlying dollar price
     */
    public function getDollarPrice(): float
    {
        return $this->coin->getDollarPrice();
    }

    /**
     * Calculate Price
     */
    public function calcPrice(float $price = null): float
    {
        if (is_null($price)) {
            $price = $this->coin->getDollarPrice();
        }

        return $this->getValue() * $price;
    }

    /**
     * Get price
     */
    public function getPrice(string $currency = 'USD', float $dollarPrice = null, bool $format = false): float|string
    {
        return convertCurrency($this->calcPrice($dollarPrice), 'USD', strtoupper($currency), $format, $this->coin->currency_precision);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPrice(string $currency = 'USD', float $dollarPrice = null): string
    {
        return convertCurrency($this->calcPrice($dollarPrice), 'USD', strtoupper($currency), true, $this->coin->currency_precision);
    }

    /**
     * Get price as money object
     */
    public function getPriceAsMoney(string $currency = 'USD', float $dollarPrice = null): Money
    {
        return app('exchanger')->convert(
            money($this->calcPrice($dollarPrice), 'USD', true, $this->coin->currency_precision),
            currency($currency, $this->coin->currency_precision)
        );
    }
}
