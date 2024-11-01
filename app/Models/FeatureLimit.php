<?php

namespace App\Models;

use Akaunting\Money\Money;
use App\Models\Support\Memoization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureLimit extends Model
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
    protected $guarded = ['name', 'created_at', 'updated_at'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'title',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'unverified_limit' => 'float',
        'basic_limit' => 'float',
        'advanced_limit' => 'float',
    ];

    /**
     * Get title attribute
     */
    protected function getTitleAttribute(): string
    {
        return trans("feature.$this->name");
    }

    /**
     * Check if feature is enabled for user
     */
    public function isEnabledFor(User $user): bool
    {
        return $this->getLimit($user) > 0;
    }

    /**
     * Get user's limit
     */
    public function getLimit(User $user): float
    {
        $status = $user->verification->getLevel();

        return $this->{"{$status}_limit"} ?: 0;
    }

    /**
     * Get total usage
     */
    public function getUsage(User $user): float
    {
        return $this->memo("usage.$user->id", function () use ($user) {
            return $this->usages()->whereDate('created_at', '>=', now()->startOf($this->period))
                ->where('user_id', $user->id)->sum('value');
        });
    }

    /**
     * Available
     */
    public function getAvailable(User $user): float
    {
        return max($this->getLimit($user) - $this->getUsage($user), 0);
    }

    /**
     * Check availability
     */
    public function checkAvailability(float|Money $value, User $user): bool
    {
        return $this->getAvailable($user) >= $this->parseValue($value);
    }

    /**
     * Set feature usage
     */
    public function setUsage(float|Money $value, User $user)
    {
        $this->usages()->create([
            'value' => $this->parseValue($value),
            'user_id' => $user->id,
        ]);
    }

    /**
     * Validate limit value
     */
    protected function parseValue(float|Money $value): float
    {
        if (is_float($value)) {
            return $value;
        }

        $precision = $value->getCurrency()->getPrecision();
        $currency = currency('USD', max($precision, 2));

        return app('exchanger')->convert($value, $currency)->getValue();
    }

    /**
     * Authorize user for this feature
     */
    public function authorize(User $user, float|Money $value): void
    {
        if (!$this->isEnabledFor($user)) {
            abort(403, trans('feature.disabled'));
        }

        if (!$this->checkAvailability($value, $user)) {
            abort(403, trans('feature.limit_reached'));
        }
    }

    /**
     * Feature usage logs
     */
    public function usages(): HasMany
    {
        return $this->hasMany(FeatureUsage::class, 'feature_name', 'name');
    }

    /**
     * Bank deposit
     */
    public static function paymentsDeposit(): FeatureLimit
    {
        return static::staticMemo('payments_deposit', function () {
            return self::findOrFail('payments_deposit');
        });
    }

    /**
     * Bank Withdrawal
     */
    public static function paymentsWithdrawal(): FeatureLimit
    {
        return static::staticMemo('payments_withdrawal', function () {
            return self::findOrFail('payments_withdrawal');
        });
    }

    /**
     * Wallet Exchange
     */
    public static function walletExchange(): FeatureLimit
    {
        return static::staticMemo('wallet_exchange', function () {
            return self::findOrFail('wallet_exchange');
        });
    }
}
