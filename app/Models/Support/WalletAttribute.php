<?php

namespace App\Models\Support;

use App\Models\Wallet;

interface WalletAttribute
{
    /**
     * Get parent wallet
     */
    public function getWallet(): Wallet;
}
