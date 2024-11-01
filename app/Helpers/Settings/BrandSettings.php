<?php

namespace App\Helpers\Settings;

class BrandSettings
{
    use InteractsWithStore;

    /**
     * Initialize attributes with default value
     */
    protected array $attributes = [
        'logo_url' => null,
        'favicon_url' => null,
        'support_url' => null,
        'terms_url' => null,
        'policy_url' => null,
    ];
}
