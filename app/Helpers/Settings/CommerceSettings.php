<?php

namespace App\Helpers\Settings;

class CommerceSettings
{
    use InteractsWithStore;

    /**
     * Initialize attributes with default value
     */
    protected array $attributes = [
        'pending_transactions' => 10,
        'transaction_interval' => 10,
    ];
}
