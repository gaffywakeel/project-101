<?php

namespace App\CoinAdapters\Contracts;

use App\CoinAdapters\Resources\Wallet;

interface NativeAsset
{
    /**
     * Get fee address to be used for funding transaction
     */
    public function getFeeAddress(Wallet $wallet): string;

    /**
     * Get fee asset identifier
     */
    public function getNativeAssetId(): string;
}
