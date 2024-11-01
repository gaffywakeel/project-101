<?php

namespace App\CoinAdapters;

use App\CoinAdapters\Abstracts\AbstractBitGoAdapter;
use Brick\Math\BigDecimal;

class TonAdapter extends AbstractBitGoAdapter
{
    /**
     * {@inheritDoc}
     */
    protected string $name = 'Toncoin';

    /**
     * {@inheritDoc}
     */
    protected string $identifier = 'ton';

    /**
     * {@inheritDoc}
     */
    protected string $baseUnit = '1000000000'; // Assuming 9 decimal places for Toncoin

    /**
     * {@inheritDoc}
     */
    protected int $precision = 9;

    /**
     * {@inheritDoc}
     */
    protected int $currencyPrecision = 2;

    /**
     * {@inheritDoc}
     */
    protected string $symbol = 'TON';

    /**
     * {@inheritDoc}
     */
    protected bool $symbolFirst = true;

    /**
     * {@inheritDoc}
     */
    protected string $color = '#2C97DE'; // Toncoin's official color

    /**
     * {@inheritDoc}
     */
    protected string $bitgoIdentifier = 'ton';

    /**
     * {@inheritDoc}
     */
    protected string $bitgoTestIdentifier = 'tton';

    /**
     * {@inheritDoc}
     */
    public function getSvgIcon(): string
    {
        return asset('coin/ton.svg'); // Replace with the correct asset path for TON
    }

    /**
     * {@inheritDoc}
     */
    public function marketId(): string
    {
        return 'toncoin'; // Market ID for Toncoin
    }

    /**
     * {@inheritDoc}
     */
    public function estimateTransactionFee(string $amount, int $inputs): string
    {
        $bitgoPercent = config('services.bitgo.fee_percent', 0.01);
        $bitgoFee = BigDecimal::of($amount)->multipliedBy($bitgoPercent);

        return (string) BigDecimal::of($this->getFeeEstimate())
            ->multipliedBy(($inputs * 360) + 78 + $inputs)->plus($bitgoFee);
    }

    /**
     * {@inheritDoc}
     */
    public function getMinimumTransferable(): string
    {
        return (string) BigDecimal::of($this->getBaseUnit())->multipliedBy(0.002);
    }

    /**
     * {@inheritDoc}
     */
    public function getMaximumTransferable(): string
    {
        return (string) BigDecimal::of($this->getBaseUnit())->multipliedBy(1000);
    }
}
