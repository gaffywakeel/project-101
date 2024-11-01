<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use RuntimeException;
use Spatie\Permission\Models\Permission as Model;

class Permission extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::updating(function (self $permission) {
            if ($permission->isDirty('name')) {
                throw new RuntimeException('Forbidden');
            }
        });
    }

    /**
     * Human friendly title
     */
    protected function title(): Attribute
    {
        return Attribute::get(function () {
            return !Lang::has($key = "permission.$this->name") ? $this->name : Lang::get($key);
        });
    }

    /**
     * Permission group
     */
    protected function group(): Attribute
    {
        return Attribute::get(function () {
            return Arr::get(explode(':', $this->name), 1, 'miscellaneous');
        });
    }
}
