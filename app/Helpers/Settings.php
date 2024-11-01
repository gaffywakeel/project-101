<?php

namespace App\Helpers;

use App\Helpers\Settings\BrandSettings;
use App\Helpers\Settings\CommerceSettings;
use App\Helpers\Settings\InteractsWithStore;
use App\Helpers\Settings\ThemeSettings;
use App\Helpers\Settings\VerificationSettings;

/**
 * @property BrandSettings $brand
 * @property VerificationSettings $verification
 * @property ThemeSettings $theme
 * @property CommerceSettings $commerce
 */
class Settings
{
    use InteractsWithStore;

    /**
     * Initialize attributes with default value
     */
    protected array $attributes = [
        'enable_mail' => false,
        'enable_database' => true,
        'enable_sms' => false,
        'user_setup' => true,
        'min_payment' => 50,
        'max_payment' => 1000,
        'price_margin' => 50,
        'price_cache' => 60,
    ];

    /**
     * Define settings' children
     *
     * @var array|string[]
     */
    protected array $children = [
        'brand' => BrandSettings::class,
        'verification' => VerificationSettings::class,
        'theme' => ThemeSettings::class,
        'commerce' => CommerceSettings::class,
    ];
}
