<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Rateable
{
    /**
     * Ratings relation
     */
    public function ratings(): MorphMany;
}
