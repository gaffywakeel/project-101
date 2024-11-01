<?php

namespace App\Models;

use App\Models\Support\Memoization;
use App\Models\Support\ValidationRules;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role as Model;
use Spatie\Permission\PermissionRegistrar;

class Role extends Model
{
    use Memoization, ValidationRules;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_protected', 'is_administrator'];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (self $role) {
            if (empty($role->order)) {
                $role->order = static::orderByDesc('order')->value('order') + 1;
            }

            if (empty($role->guard_name)) {
                $role->guard_name = config('permission.defaults.guard', 'web');
            }
        });

        static::updating(function (self $role) {
            if ($role->isDirty('name') && $role->is_protected) {
                throw new Exception('Forbidden');
            }
        });

        static::deleting(function (self $role) {
            if ($role->is_protected) {
                throw new Exception('Forbidden');
            }
        });
    }

    /**
     * Check if array is protected
     */
    protected function isProtected(): Attribute
    {
        return Attribute::get(function () {
            $name = $this->getOriginal('name');

            $data = collect(config('permission.defaults.roles'))
                ->where('name', $name)->first();

            return Arr::get($data, 'protected', false);
        });
    }

    /**
     * Next Role in hierarchy
     */
    public function next(): ?Role
    {
        return $this->memo('next', function () {
            return static::whereKeyNot($this->getKey())
                ->where('order', '>', $this->order)
                ->orderBy('order')->first();
        });
    }

    /**
     * Previous Role in hierarchy
     */
    public function previous(): ?Role
    {
        return $this->memo('previous', function () {
            return static::whereKeyNot($this->getKey())
                ->where('order', '<', $this->order)
                ->orderByDesc('order')->first();
        });
    }

    /**
     * Check if this is administrator role
     */
    protected function isAdministrator(): Attribute
    {
        return Attribute::get(function (): bool {
            return $this->is(Role::administrator());
        });
    }

    /**
     * Get "Administrator" Role
     */
    public static function administrator(): Role
    {
        return static::staticMemo('role.administrator', function () {
            $name = config('permission.defaults.roles.administrator.name');

            return self::where('name', $name)->firstOrFail();
        });
    }

    /**
     * Get "Operator" Role
     */
    public static function operator(): Role
    {
        return static::staticMemo('role.operator', function () {
            $name = config('permission.defaults.roles.operator.name');

            return self::where('name', $name)->firstOrFail();
        });
    }

    /**
     * Get "Demo" Role
     */
    public static function demo(): Role
    {
        return static::staticMemo('role.demo', function () {
            $name = config('permission.defaults.roles.demo.name');

            return self::where('name', $name)->firstOrFail();
        });
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard('web'),
            'model',
            config('permission.table_names.model_has_roles'),
            app(PermissionRegistrar::class)->pivotRole,
            config('permission.column_names.model_morph_key')
        );
    }
}
