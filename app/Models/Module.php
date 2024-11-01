<?php

namespace App\Models;

use App\Models\Support\Memoization;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Module extends Model
{
    use HasFactory, Memoization;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'name';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['name', 'operator_id', 'created_at', 'updated_at'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['operator'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Check if module is enabled
     */
    public function isEnabled(): bool
    {
        return $this->status;
    }

    /**
     * Get operator for User
     *
     *
     * @throws AuthorizationException
     */
    public function operatorFor(User $user): User
    {
        if (!$this->operator) {
            throw new AuthorizationException(trans('miscellaneous.operator_unavailable'));
        }

        if ($this->operator->is($user)) {
            throw new AuthorizationException(trans('miscellaneous.operator_cannot_trade'));
        }

        return $this->operator;
    }

    /**
     * Check if operator exists
     */
    public function hasOperator(): bool
    {
        return $this->operator()->exists();
    }

    /**
     * Get operator's account.
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id', 'id')->role(Role::operator());
    }

    /**
     * Get stake module
     */
    public static function stake(): Module
    {
        return static::staticMemo('stake', function () {
            return self::findOrFail('stake');
        });
    }

    /**
     * Get exchange module
     */
    public static function exchange(): Module
    {
        return static::staticMemo('exchange', function () {
            return self::findOrFail('exchange');
        });
    }

    /**
     * Get payment module
     */
    public static function payment(): Module
    {
        return static::staticMemo('payment', function () {
            return self::findOrFail('payment');
        });
    }

    /**
     * Get commerce module
     */
    public static function commerce(): Module
    {
        return static::staticMemo('commerce', function () {
            return self::findOrFail('commerce');
        });
    }

    /**
     * Get peer module
     */
    public static function peer(): Module
    {
        return static::staticMemo('peer', function () {
            return self::findOrFail('peer');
        });
    }

    /**
     * Get wallet module
     */
    public static function wallet(): Module
    {
        return static::staticMemo('wallet', function () {
            return self::findOrFail('wallet');
        });
    }
}
