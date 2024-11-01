<?php

namespace App\Models\Support;

use Akaunting\Money\Currency;

interface CurrencyAttribute
{
    /**
     * Get currency attribute
     */
    public function getCurrency(): Currency;
}
