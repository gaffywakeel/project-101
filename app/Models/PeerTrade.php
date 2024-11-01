<?php

namespace App\Models;

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use App\Casts\CoinFormatterCast;
use App\Casts\MoneyCast;
use App\Exceptions\LockException;
use App\Exceptions\TransferException;
use App\Helpers\CoinFormatter;
use App\Models\Support\CurrencyAttribute;
use App\Models\Support\Lock;
use App\Models\Support\Memoization;
use App\Models\Support\Uuid;
use App\Models\Support\WalletAttribute;
use App\Notifications\PeerTradeCanceled;
use App\Notifications\PeerTradeCompleted;
use App\Notifications\PeerTradeConfirmed;
use App\Notifications\PeerTradeDisputed;
use App\Notifications\PeerTradeStarted;
use Exception;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Musonza\Chat\Chat;
use Musonza\Chat\Models\Conversation;

class PeerTrade extends Model implements WalletAttribute, CurrencyAttribute
{
    use HasFactory, BroadcastsEvents, Lock, Uuid, Memoization;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'instruction',
        'canceled_at',
        'confirmed_at',
        'disputed_at',
        'completed_at',
        'disputed_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount' => MoneyCast::class,
        'total_value' => CoinFormatterCast::class,
        'value' => CoinFormatterCast::class,
        'fee_value' => CoinFormatterCast::class,
        'price' => MoneyCast::class . ':false',
        'canceled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'disputed_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'formatted_amount',
        'buyer',
        'seller',
        'coin',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['instruction'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['buyerWalletAccount', 'sellerWalletAccount', 'offer', 'paymentMethod', 'bankAccount', 'conversation', 'sellerRating', 'buyerRating'];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (self $record) {
            $record->sendAutoReply();
            $record->seller->notify(new PeerTradeStarted($record));
            $record->buyer->notify(new PeerTradeStarted($record));
        });

        static::updating(function (self $record) {
            if ($record->isDirty('status')) {
                $attribute = match ($record->status) {
                    'canceled' => 'canceled_at',
                    'completed' => 'completed_at',
                    'disputed' => 'disputed_at',
                    default => null
                };

                if (is_string($attribute)) {
                    $record->$attribute = Date::now();
                }
            }
        });

        static::updated(function (self $record) {
            if ($record->isDirty('status')) {
                $notification = match ($record->status) {
                    'canceled' => new PeerTradeCanceled($record),
                    'completed' => new PeerTradeCompleted($record),
                    'disputed' => new PeerTradeDisputed($record),
                    default => null
                };

                if ($notification instanceof Notification) {
                    $record->seller->notify($notification);
                    $record->buyer->notify($notification);
                }
            }

            if ($record->isDirty('confirmed_at') && $record->confirmed) {
                $record->seller->notify(new PeerTradeConfirmed($record));
                $record->buyer->notify(new PeerTradeConfirmed($record));
            }
        });

        static::saving(CoinFormatterCast::assert('total_value', 'value', 'fee_value'));
        static::saving(MoneyCast::assert('amount', 'price'));
    }

    /**
     * Get short id
     */
    protected function getShortIdAttribute(): string
    {
        return substr($this->id, 0, 8);
    }

    /**
     * Get amount object
     */
    public function getAmountObject(): Money
    {
        return $this->amount;
    }

    /**
     * formatted_amount Attribute
     */
    protected function getFormattedAmountAttribute(): string
    {
        return $this->getAmountObject()->format();
    }

    /**
     * total_value Object
     */
    public function getTotalValueObject(): CoinFormatter
    {
        return $this->total_value;
    }

    /**
     * fee_value object
     */
    protected function getFeeValueObject(): CoinFormatter
    {
        return $this->fee_value;
    }

    /**
     * Get "value" object
     */
    public function getValueObject(): CoinFormatter
    {
        return $this->value;
    }

    /**
     * Get price object
     */
    public function getPriceObject(): Money
    {
        return $this->price;
    }

    /**
     * Get formatted_price attribute
     */
    protected function getFormattedPriceAttribute(): string
    {
        return $this->getPriceObject()->format();
    }

    /**
     * Get buyer attribute
     */
    protected function getBuyerAttribute(): User
    {
        return $this->buyerWalletAccount->user;
    }

    /**
     * Get seller attribute
     */
    protected function getSellerAttribute(): User
    {
        return $this->sellerWalletAccount->user;
    }

    /**
     * Get coin attribute
     */
    protected function getCoinAttribute(): Coin
    {
        return $this->sellerWalletAccount->wallet->coin;
    }

    /**
     * Get confirmed status
     */
    protected function getConfirmedAttribute(): bool
    {
        return (bool) $this->confirmed_at;
    }

    /**
     * Get expires_at attribute
     */
    protected function getExpiresAtAttribute(): Carbon
    {
        return $this->created_at->clone()->addMinutes($this->time_limit);
    }

    /**
     * Check if trade is expired
     */
    protected function getExpiredAttribute(): bool
    {
        return $this->expires_at->isBefore(now());
    }

    /**
     * Get the timestamp after which trade is disputable
     */
    protected function getDisputableFromAttribute(): ?Carbon
    {
        return $this->confirmed_at?->clone()->addMinutes($this->time_limit);
    }

    /**
     * Get disputable attribute
     */
    protected function isDisputable(): bool
    {
        return $this->status === 'active' && $this->confirmed && $this->disputable_from?->isBefore(now());
    }

    /**
     * Get confirmable attribute
     */
    protected function isConfirmable(): bool
    {
        return $this->status === 'active' && !$this->confirmed && !$this->expired;
    }

    /**
     * Check if trade is in progress
     */
    protected function getInProgressAttribute(): bool
    {
        return in_array($this->status, ['active', 'disputed']);
    }

    /**
     * Complete trade
     *
     *
     * @throws LockException
     */
    public function complete(): PeerTrade
    {
        if (!$this->in_progress || !$this->confirmed) {
            throw new TransferException('Trade cannot be completed.');
        }

        return $this->sellerWalletAccount->acquireLockOrThrow(function (WalletAccount $sellerWalletAccount) {
            if ($sellerWalletAccount->getAvailableObject()->isNegative()) {
                throw new TransferException('Seller has negative balance.');
            }

            return DB::transaction(function () use ($sellerWalletAccount) {
                $sellerDescription = $this->getTransferDescription($this->seller);
                $buyerDescription = $this->getTransferDescription($this->buyer);

                $sellerWalletAccount->debit($this->getTotalValueObject(), $sellerDescription);

                $transferable = $this->getTotalValueObject()->subtract($this->getFeeValueObject());
                $operatorWalletAccount = $this->getOperatorWalletAccount();

                if ($this->getFeeValueObject()->isPositive() && $operatorWalletAccount) {
                    $operatorDescription = $this->getTransferDescription($operatorWalletAccount->user);
                    $operatorWalletAccount->credit($this->getFeeValueObject(), $operatorDescription);
                }

                $this->buyerWalletAccount->credit($transferable, $buyerDescription);

                return tap($this)->update(['status' => 'completed']);
            });
        });
    }

    /**
     * Get transfer description
     */
    public function getTransferDescription(User $recipient): string
    {
        return match (true) {
            $recipient->is($this->buyer) => trans('peer.buy_description', [
                'coin' => $this->coin->name,
                'name' => $this->seller->name,
            ]),
            $recipient->is($this->seller) => trans('peer.sell_description', [
                'coin' => $this->coin->name,
                'name' => $this->buyer->name,
            ]),
            default => trans('peer.fee_description', [
                'buyer' => $this->buyer->name,
                'seller' => $this->seller->name,
            ])
        };
    }

    /**
     * Check if trade should auto cancel
     */
    public function shouldAutoCancel(): bool
    {
        return $this->status === 'active' && !$this->confirmed && $this->expired;
    }

    /**
     * Check if trade is cancelable by user
     */
    public function cancelableBy(User $user): bool
    {
        return match ($this->status) {
            'active' => $user->is($this->buyer),
            'disputed' => $user->can('manage:peer_trades'),
            default => false
        };
    }

    /**
     * Check if trade is confirmable by user
     */
    public function confirmableBy(User $user): bool
    {
        return $user->is($this->buyer) && $this->isConfirmable();
    }

    /**
     * Check if trade can be disputed by user
     */
    public function disputableBy(User $user): bool
    {
        return ($user->is($this->buyer) || $user->is($this->seller)) && $this->isDisputable();
    }

    /**
     * Check if buyer can be rated
     */
    public function buyerRatableBy(User $user): bool
    {
        return $this->status === 'completed' && $user->is($this->seller);
    }

    /**
     * Check if seller can be rated
     */
    public function sellerRatableBy(User $user): bool
    {
        return $this->status === 'completed' && $user->is($this->buyer);
    }

    /**
     * Check if trade is completable by user
     */
    public function completableBy(User $user): bool
    {
        $allowed = match ($this->status) {
            'active' => $user->is($this->seller),
            'disputed' => $user->can('manage:peer_trades'),
            default => false
        };

        return $this->confirmed && $allowed;
    }

    /**
     * Allow participation
     */
    public function allowParticipation(User $user): bool
    {
        return $user->can('manage:peer_trades') && $this->status === 'disputed';
    }

    /**
     * Broadcast trade
     */
    public function broadcastOn($event): array
    {
        return [$this];
    }

    /**
     * Get the data to broadcast for the model.
     */
    public function broadcastWith($event): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'in_progress' => $this->in_progress,
            'confirmed' => $this->confirmed,
            'expired' => $this->expired,
            'canceled_at' => $this->canceled_at,
            'confirmed_at' => $this->confirmed_at,
            'completed_at' => $this->completed_at,
            'disputed_at' => $this->disputed_at,
            'disputed_by' => $this->disputed_by,
        ];
    }

    /**
     * Get role of user
     */
    public function getRole(User $user): ?string
    {
        return match (true) {
            $user->is($this->seller) => 'seller',
            $user->is($this->buyer) => 'buyer',
            default => null
        };
    }

    /**
     * Check if user is participant of the trade
     */
    public function hasParticipant(User $user): bool
    {
        return $this->conversation->participants()->where([
            'messageable_id' => $user->getKey(),
            'messageable_type' => $user->getMorphClass(),
        ])->exists();
    }

    /**
     * Check if trade is visible to user
     */
    public function isVisibleTo(User $user): bool
    {
        return $user->is($this->seller) || $user->is($this->buyer) || $user->can('manage:peer_trades');
    }

    /**
     * Get operator wallet account
     */
    public function getOperatorWalletAccount(): ?WalletAccount
    {
        $operator = Module::peer()->operator;

        $wallet = $this->sellerWalletAccount->wallet;

        return $operator?->getWalletAccount($wallet);
    }

    /**
     * Scope inProgressOrCompleted query
     */
    public function scopeInProgressOrCompleted(Builder $query): Builder
    {
        return $query->whereIn('status', ['active', 'completed', 'disputed']);
    }

    /**
     * Scope inProgress query
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->whereIn('status', ['active', 'disputed']);
    }

    /**
     * Start conversation
     *
     *
     * @throws Exception
     */
    public function sendAutoReply(): void
    {
        if ($this->offer?->auto_reply) {
            app(Chat::class)
                ->message($this->offer->auto_reply)
                ->from($this->offer->walletAccount->user)
                ->to($this->conversation)->send();
        }
    }

    /**
     * Get unread messages
     */
    public function getUnreadMessages(User $user): int
    {
        return app(Chat::class)
            ->conversation($this->conversation)
            ->setParticipant($user)
            ->unreadCount();
    }

    /**
     * Chat conversation
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'chat_conversation_id', 'id');
    }

    /**
     * Buyer wallet account
     */
    public function buyerWalletAccount(): BelongsTo
    {
        return $this->belongsTo(WalletAccount::class, 'buyer_wallet_account_id', 'id');
    }

    /**
     * Seller wallet account
     */
    public function sellerWalletAccount(): BelongsTo
    {
        return $this->belongsTo(WalletAccount::class, 'seller_wallet_account_id', 'id');
    }

    /**
     * Related offer
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(PeerOffer::class, 'offer_id', 'id');
    }

    /**
     * Related payment method
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PeerPaymentMethod::class, 'payment_method_id', 'id');
    }

    /**
     * Related bank account
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id', 'id');
    }

    /**
     * Seller rating
     */
    public function sellerRating(): BelongsTo
    {
        return $this->belongsTo(Rating::class, 'seller_rating_id', 'id');
    }

    /**
     * Buyer rating
     */
    public function buyerRating(): BelongsTo
    {
        return $this->belongsTo(Rating::class, 'buyer_rating_id', 'id');
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrency(): Currency
    {
        return currency($this->currency);
    }

    /**
     * {@inheritDoc}
     */
    public function getWallet(): Wallet
    {
        return $this->sellerWalletAccount->wallet;
    }
}
