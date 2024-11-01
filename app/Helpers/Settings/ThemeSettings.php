<?php

namespace App\Helpers\Settings;

class ThemeSettings
{
    use InteractsWithStore;

    /**
     * Initialize attributes with default value
     */
    protected array $attributes = [
        'mode' => 'dark',
        'direction' => 'ltr',
        'color' => 'orange',
    ];
}
