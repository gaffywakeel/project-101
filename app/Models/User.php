<?php

namespace App\Models;

use Akaunting\Money\Currency;
use App\Events\UserActivities\EmailChanged;
use App\Events\UserActivities\PhoneChanged;
use App\Events\UserPresenceChanged;
use App\Helpers\Token;
use App\Helpers\TwoFactorAuth;
use App\Helpers\UserVerification;
use App\Models\Support\Comparison;
use App\Models\Support\HasRatings;
use App\Models\Support\Lock;
use App\Models\Support\Memoization;
use App\Models\Support\Rateable;
use App\Models\Support\ValidationRules;
use App\Notifications\Auth\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Musonza\Chat\Traits\Messageable;
use RuntimeException;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, Rateable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, Lock, HasRatings, Messageable, Memoization, Comparison, ValidationRules;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'deactivated_until',
        'password',
        'country',
        'currency',
        'two_factor_enable',
        'phone_verified_at',
        'email_verified_at',
        'presence',
        'notifications_read_at',
        'last_seen_at',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'email',
        'phone',
        'two_factor_secret',
        'password',
        'remember_token',
        'roles',
        'permissions',
        'activities',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'two_factor_secret' => 'encrypted',
        'two_factor_enable' => 'boolean',
        'notifications_read_at' => 'datetime',
        'deactivated_until' => 'datetime',
        'last_seen_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['profile'];

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
        static::creating(function ($user) {
            $user->two_factor_secret = app(TwoFactorAuth::class)->generateSecretKey();
        });

        static::updating(function (self $user) {
            if ($user->isDirty('email')) {
                event(new EmailChanged($user));
                $user->email_verified_at = null;
            }

            if ($user->isDirty('presence') && $user->presence === 'online') {
                $user->last_seen_at = $user->freshTimestamp();
            }

            if ($user->isDirty('phone')) {
                event(new PhoneChanged($user));
                $user->phone_verified_at = null;
            }
        });

        static::created(function (self $user) {
            $user->profile()->save(new UserProfile);
        });
    }

    /**
     * Get path for profile
     */
    public function path(): string
    {
        return "profile/{$this->id}";
    }

    /**
     * Determine if the user has enabled two factor
     */
    public function isTwoFactorEnabled(): bool
    {
        return $this->two_factor_enable;
    }

    /**
     * Reset two factor
     */
    public function resetTwoFactorSecret(): void
    {
        $this->two_factor_secret = app(TwoFactorAuth::class)->generateSecretKey();
        $this->save();
    }

    /**
     * Disable two factor
     */
    public function disableTwoFactor(): void
    {
        $this->two_factor_enable = false;
        $this->save();
    }

    /**
     * Enable two factor
     */
    public function enableTwoFactor(): void
    {
        $this->two_factor_enable = true;
        $this->save();
    }

    /**
     * Get the two-factor authentication QR code URL.
     */
    public function getTwoFactorUrl(): string
    {
        return app(TwoFactorAuth::class)->qrCodeUrl($this->email, $this->two_factor_secret);
    }

    /**
     * Verify two-factor token
     */
    public function verifyTwoFactorToken($token): bool
    {
        return app(TwoFactorAuth::class)->verify($this->two_factor_secret, $token);
    }

    /**
     * Generate two-factor token
     */
    public function generateTwoFactorToken(): string
    {
        return app(TwoFactorAuth::class)->generateCode($this->two_factor_secret);
    }

    /**
     * Generate phone token
     */
    public function generatePhoneToken(): array
    {
        return app(Token::class)->generate($this->phone);
    }

    /**
     * Validate phone token
     */
    public function validatePhoneToken(string $token): bool
    {
        return app(Token::class)->validate($this->phone, $token);
    }

    /**
     * Generate email token
     */
    public function generateEmailToken(): array
    {
        return app(Token::class)->generate($this->email);
    }

    /**
     * Validate email token
     */
    public function validateEmailToken(string $token): bool
    {
        return app(Token::class)->validate($this->email, $token);
    }

    /**
     * Check if user is administrator
     */
    protected function isAdministrator(): Attribute
    {
        return Attribute::get(function (): bool {
            return $this->hasRole(Role::administrator());
        });
    }

    /**
     * Get location activity
     */
    protected function location(): Attribute
    {
        return Attribute::get(function (): ?array {
            return $this->activities()->latest()->first()?->location;
        })->shouldCache();
    }

    /**
     * Country operation status
     */
    protected function countryOperation(): Attribute
    {
        return Attribute::get(function (): bool {
            return is_string($this->country) && OperatingCountry::where('code', $this->country)->exists();
        })->shouldCache();
    }

    /**
     * Get currency
     */
    protected function currency(): Attribute
    {
        return Attribute::get(function ($value): string {
            return SupportedCurrency::findByCode($value) ? strtoupper($value) : defaultCurrency();
        })->shouldCache();
    }

    /**
     * Get currency name
     */
    protected function currencyName(): Attribute
    {
        return Attribute::get(fn () => (new Currency($this->currency))->getName())->shouldCache();
    }

    /**
     * Check if user's phone is verified
     */
    public function isPhoneVerified(): bool
    {
        return (bool) $this->phone_verified_at;
    }

    /**
     * Check if user's email is verified
     */
    public function isEmailVerified(): bool
    {
        return (bool) $this->email_verified_at;
    }

    /**
     * Get "rank" by roles order
     */
    protected function rank(): ?int
    {
        return $this->memo('rank', function (): ?int {
            return $this->roles()->orderBy('order')->first()?->order;
        });
    }

    /**
     * Check if user is superior to another
     */
    public function superiorTo(self|int $subject): bool
    {
        if (is_null($this->rank())) {
            return false;
        }

        return $this->subordinates()->whereKey($subject)->exists();
    }

    /**
     * Query subordinates
     */
    public function subordinates(): Builder
    {
        if (is_null($this->rank())) {
            throw new RuntimeException('Missing rank.');
        }

        if ($this->hasRole(Role::administrator())) {
            return self::query()->whereKeyNot($this->getKey());
        }

        return self::query()->whereKeyNot($this->getKey())
            ->withoutRole(Role::administrator())
            ->whereDoesntHave('roles', function (Builder $query) {
                $query->where('order', '<', $this->rank());
            });
    }

    /**
     * long_term attribute
     */
    protected function getLongTermAttribute(): bool
    {
        return $this->isLongTerm();
    }

    /**
     * Active attribute
     */
    protected function getActiveAttribute(): bool
    {
        return $this->isActive();
    }

    /**
     * Log user activity
     *
     * @param  null  $source
     * @param  null  $agent
     */
    public function log(string $action, string $ip, $source = null, $agent = null): Model
    {
        return $this->activities()->create([
            'action' => $action,
            'source' => $source,
            'ip' => $ip,
            'location' => geoip($ip)->toArray(),
            'agent' => $agent,
        ]);
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Laravel permissions guard
     */
    protected function getDefaultGuardName(): string
    {
        return config('permission.defaults.guard');
    }

    /**
     * Route notifications for the Vonage channel.
     */
    public function routeNotificationForVonage(): string
    {
        return preg_replace('/\D+/', '', $this->phone);
    }

    /**
     * Route notifications for the SNS channel.
     */
    public function routeNotificationForSns(): string
    {
        return $this->phone;
    }

    /**
     * Route notifications for the Twilio channel.
     */
    public function routeNotificationForTwilio(): string
    {
        return $this->phone;
    }

    /**
     * Route notifications for the Africas Talking channel.
     */
    public function routeNotificationForAfricasTalking(): string
    {
        return $this->phone;
    }

    /**
     * User profile
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    /**
     * Get wallet address label
     */
    public function walletLabel(): string
    {
        return $this->email;
    }

    /**
     * Get user roles
     */
    protected function allRoles(): Attribute
    {
        return Attribute::get(function (): array {
            return $this->roles->sortBy('order')->pluck('name')->toArray();
        })->shouldCache();
    }

    /**
     * Get user permissions
     */
    protected function allPermissions(): Attribute
    {
        return Attribute::get(function (): array {
            return $this->getAllPermissions()->pluck('name')->toArray();
        })->shouldCache();
    }

    /**
     * Get participation details
     */
    public function getParticipantDetails(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'presence' => $this->presence,
            'last_seen_at' => $this->last_seen_at,
            'picture' => $this->profile->picture,
        ];
    }

    /**
     * Get country name
     */
    protected function countryName(): Attribute
    {
        return Attribute::get(fn () => config("countries.$this->country"));
    }

    /**
     * Update authenticated user's presence
     */
    public function updatePresence(string $presence): void
    {
        $this->update(['presence' => $presence]);
        broadcast(new UserPresenceChanged($this));
    }

    /**
     * Check if user is deactivated
     */
    public function isDeactivated(): bool
    {
        return $this->deactivated_until && $this->deactivated_until > now();
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return !$this->isDeactivated();
    }

    /**
     * Check if this is a "long term" user
     */
    public function isLongTerm(): bool
    {
        return now()->diffInMonths($this->created_at) >= 3;
    }

    /**
     * User's wallet accounts
     */
    public function walletAccounts(): HasMany
    {
        return $this->hasMany(WalletAccount::class, 'user_id', 'id');
    }

    /**
     * User's activities
     */
    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class, 'user_id', 'id');
    }

    /**
     * User's transfer records
     */
    public function transferRecords(): HasManyThrough
    {
        return $this->hasManyThrough(TransferRecord::class, WalletAccount::class, 'user_id', 'wallet_account_id');
    }

    /**
     * User's payment transactions
     */
    public function paymentTransactions(): HasManyThrough
    {
        return $this->hasManyThrough(PaymentTransaction::class, PaymentAccount::class, 'user_id', 'payment_account_id');
    }

    /**
     * Related ExchangeTrade
     */
    public function exchangeTrades(): HasManyThrough
    {
        return $this->hasManyThrough(ExchangeTrade::class, WalletAccount::class, 'user_id', 'wallet_account_id');
    }

    /**
     * Related ExchangeSwap
     */
    public function exchangeSwaps(): HasManyThrough
    {
        return $this->hasManyThrough(ExchangeSwap::class, WalletAccount::class, 'user_id', 'sell_wallet_account_id');
    }

    /**
     * User's sell trades
     */
    public function sellPeerTrades(): HasManyThrough
    {
        return $this->hasManyThrough(PeerTrade::class, WalletAccount::class, 'user_id', 'seller_wallet_account_id');
    }

    /**
     * User's buy trades
     */
    public function buyPeerTrades(): HasManyThrough
    {
        return $this->hasManyThrough(PeerTrade::class, WalletAccount::class, 'user_id', 'buyer_wallet_account_id');
    }

    /**
     * User's stakes
     */
    public function stakes(): HasManyThrough
    {
        return $this->hasManyThrough(Stake::class, WalletAccount::class, 'user_id', 'wallet_account_id');
    }

    /**
     * Get notification settings
     */
    public function notificationSettings(): HasMany
    {
        $config = config('notifications.settings');

        return $this->hasMany(UserNotificationSetting::class, 'user_id', 'id')
            ->whereIn('name', array_keys($config));
    }

    /**
     * Get notification settings
     */
    public function getNotificationSettings(): Collection
    {
        return $this->memo('notification_settings', function () {
            $this->updateNotificationSettings();

            return $this->notificationSettings;
        });
    }

    /**
     * Update user settings
     */
    protected function updateNotificationSettings(): void
    {
        $config = config('notifications.settings', []);
        $settings = $this->notificationSettings;

        collect(array_keys($config))->diff($settings->pluck('name'))->each(function ($name) use ($config) {
            $this->notificationSettings()->updateOrCreate(compact('name'), [
                'email' => (bool) data_get($config, "$name.email"),
                'database' => (bool) data_get($config, "$name.database"),
                'sms' => (bool) data_get($config, "$name.sms"),
            ]);
        });
    }

    /**
     * Get user's documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(UserDocument::class, 'user_id', 'id');
    }

    /**
     * User's address
     */
    public function address(): HasOne
    {
        return $this->hasOne(UserAddress::class, 'user_id', 'id');
    }

    /**
     * User's payment accounts
     */
    public function paymentAccounts(): HasMany
    {
        return $this->hasMany(PaymentAccount::class, 'user_id', 'id')->has('supportedCurrency');
    }

    /**
     * Get followers
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'followed_id', 'follower_id')
            ->withPivot('blocked')->withTimestamps();
    }

    /**
     * Get follower
     */
    public function getFollowerPivot(self $user): ?Pivot
    {
        return $this->followers()->find($user->id)?->pivot;
    }

    /**
     * Get following
     */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'followed_id')
            ->withPivot('blocked')->withTimestamps();
    }

    /**
     * Check if user is following another
     */
    public function isFollowing(User $user): bool
    {
        return $this->following()->whereKey($user->id)->exists();
    }

    /**
     * Get following
     */
    public function getFollowingPivot(self $user): ?Pivot
    {
        return $this->following()->find($user->id)?->pivot;
    }

    /**
     * Current payment account.
     */
    public function getPaymentAccount(): PaymentAccount
    {
        return $this->getPaymentAccountByCurrency($this->currency);
    }

    /**
     * Get payment account by currency
     */
    public function getPaymentAccountByCurrency(string $currency): PaymentAccount
    {
        return $this->paymentAccounts()->where('currency', $currency)->firstOr(function () use ($currency) {
            return $this->paymentAccounts()->create(['currency' => $currency]);
        });
    }

    /**
     * User's bank accounts
     */
    public function activeBankAccounts(): HasMany
    {
        return $this->bankAccounts()->where('currency', $this->currency)->where(function ($query) {
            $query->whereHas('bank.operatingCountries', function (Builder $query) {
                $query->where('code', $this->country);
            })->orDoesntHave('bank');
        });
    }

    /**
     * User's BankAccounts
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class, 'user_id', 'id')->has('supportedCurrency');
    }

    /**
     * User's commerce account
     */
    public function commerceAccount(): HasOne
    {
        return $this->hasOne(CommerceAccount::class, 'user_id', 'id');
    }

    /**
     * Get operating banks
     */
    public function operatingBanks(): Builder
    {
        return Bank::country($this->country);
    }

    /**
     * Get deposit bank account
     */
    public function getDepositBankAccount(): ?BankAccount
    {
        return BankAccount::doesntHave('user')->has('supportedCurrency')
            ->whereHas('bank.operatingCountries', fn (Builder $query) => $query->where('code', $this->country))
            ->where('currency', $this->currency)->latest()->first();
    }

    /**
     * Get verification helper
     */
    protected function verification(): Attribute
    {
        return Attribute::get(function (): UserVerification {
            return UserVerification::make($this);
        });
    }

    /**
     * Administrator users
     */
    public function scopeAdministrator(Builder $query): Builder
    {
        return $query->role(Role::administrator())->latest();
    }

    /**
     * Operator users
     */
    public function scopeOperator(Builder $query): Builder
    {
        return $query->role(Role::operator())->latest();
    }

    /**
     * Demo users
     */
    public function scopeDemo(Builder $query): Builder
    {
        return $query->role(Role::demo())->latest();
    }

    /**
     * Get user's WalletAccount
     */
    public function getWalletAccount(Wallet $wallet): WalletAccount
    {
        return $wallet->getAccount($this);
    }

    /**
     * Rate model
     */
    public function rate(Rateable $rateable, int $value, string $comment = null): Rating
    {
        $rating = new Rating();

        $rating->value = min($value, 5);
        $rating->comment = $comment;
        $rating->user()->associate($this);

        $rateable->ratings()->save($rating);

        return $rating;
    }

    /**
     * Rate model once
     */
    public function rateOnce(Rateable $rateable, int $value, string $comment = null): Rating
    {
        $query = $rateable->ratings()->where('user_id', $this->id);

        if ($rating = $query->first()) {
            $rating->value = min($value, 5);
            $rating->comment = $comment;

            return tap($rating)->save();
        } else {
            return $this->rate($rateable, $value, $comment);
        }
    }
}
