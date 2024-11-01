<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;

trait Comparison
{
    /**
     * Check if model is equal to another
     */
    public function isEqualTo(Model|int|string $modelOrKey): bool
    {
        return $modelOrKey instanceof Model ? $this->is($modelOrKey) : $this->getKey() === $modelOrKey;
    }

    /**
     * Check if model is not equal to another
     */
    public function isNotEqualTo(Model|int|string $modelOrKey): bool
    {
        return !$this->isEqualTo($modelOrKey);
    }
}
