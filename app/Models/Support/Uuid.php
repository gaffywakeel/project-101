<?php

namespace App\Models\Support;

use Illuminate\Support\Str;

trait Uuid
{
    /**
     * Generate a primary UUID for the model.
     */
    protected static function bootUuid(): void
    {
        static::creating(function (self $model) {
            $column = $model->getKeyName();

            if (empty($model->{$column})) {
                $model->{$column} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the auto-incrementing key type.
     */
    public function getKeyType(): string
    {
        return 'string';
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     */
    public function getIncrementing(): bool
    {
        return false;
    }
}
