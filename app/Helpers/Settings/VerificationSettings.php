<?php

namespace App\Helpers\Settings;

class VerificationSettings
{
    use InteractsWithStore;

    /**
     * Initialize attributes with default value
     */
    protected array $attributes = [
        'verified_phone' => true,
        'verified_email' => true,
        'complete_profile' => true,
        'verified_documents' => true,
        'verified_address' => true,
    ];
}
