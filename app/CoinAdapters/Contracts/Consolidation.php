<?php

namespace App\CoinAdapters\Contracts;

use App\CoinAdapters\Resources\Wallet;

interface Consolidation
{
    /**
     * Consolidate funds from the address
     */
    public function consolidate(Wallet $wallet, string $address, string $passphrase): void;
}
