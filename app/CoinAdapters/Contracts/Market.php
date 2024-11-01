<?php

namespace App\CoinAdapters\Contracts;

interface Market
{
    /**
     * Market identifier
     */
    public function marketId(): string;
}
