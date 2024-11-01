<?php

namespace App\Models;

use App\Exceptions\TransferException;
use App\Helpers\CoinFormatter;
use App\Models\Support\Lock;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use LogicException;
use RuntimeException;

class ExchangeSwap extends Model
{
    use HasFactory, Lock, BroadcastsEvents;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'buy_dollar_price' => 'float',
        'sell_dollar_price' => 'float',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['sell_wallet', 'buy_wallet'];

    /**
     * Perform any actions required after the model boots.
     */
    protected static function booted(): void
    {
        static::updating(function (self $model) {
            if ($model->isDirty('status')) {
                $attribute = match ($model->status) {
                    'completed' => 'completed_at',
                    default => null
                };

                if (is_string($attribute)) {
                    $model->$attribute = Date::now();
                }
            }
        });
    }

    /**
     * Get "sell_value" attribute
     */
    protected function sellValue(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->sellWalletAccount->wallet->castCoin($value),
            set: fn (CoinFormatter $value) => $value->getAmount()
        );
    }

    /**
     * Get "sell_value_price" attribute
     */
    protected function sellValuePrice(): Attribute
    {
        return Attribute::get(fn () => $this->sell_value->getPrice($this->sellWalletAccount->user->currency, $this->sell_dollar_price));
    }

    /**
     * Get "formatted_sell_value_price" attribute
     */
    protected function formattedSellValuePrice(): Attribute
    {
        return Attribute::get(fn () => $this->sell_value->getFormattedPrice($this->sellWalletAccount->user->currency, $this->sell_dollar_price));
    }

    /**
     * Get "buy_value" attribute
     */
    protected function buyValue(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->buyWalletAccount->wallet->castCoin($value),
            set: fn (CoinFormatter $value) => $value->getAmount()
        );
    }

    /**
     * Get "buy_value_price" attribute
     */
    protected function buyValuePrice(): Attribute
    {
        return Attribute::get(fn () => $this->buy_value->getPrice($this->buyWalletAccount->user->currency, $this->buy_dollar_price));
    }

    /**
     * Get "formatted_buy_value_price" attribute
     */
    protected function formattedBuyValuePrice(): Attribute
    {
        return Attribute::get(fn () => $this->buy_value->getFormattedPrice($this->buyWalletAccount->user->currency, $this->buy_dollar_price));
    }

    /**
     * Check if swap is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Complete ExchangeSwap
     */
    public function approve(): void
    {
        $this->assertPending();

        $this->sellWalletAccount->acquireLockOrThrow(function (WalletAccount $sellWalletAccount) {
            $operator = Module::exchange()->operatorFor($sellWalletAccount->user);

            $operatorSellWalletAccount = $this->buyWalletAccount->wallet->getAccount($operator);
            $operatorBuyWalletAccount = $sellWalletAccount->wallet->getAccount($operator);

            $operatorSellWalletAccount->acquireLockOrThrow(function (WalletAccount $operatorSellWalletAccount) use ($operatorBuyWalletAccount) {
                DB::transaction(function () use ($operatorSellWalletAccount, $operatorBuyWalletAccount) {
                    $description = $this->getTransferDescription();

                    if ($operatorSellWalletAccount->getAvailableObject()->lessThan($this->buy_value)) {
                        throw new TransferException(trans('wallet.insufficient_operator_balance'));
                    }

                    $this->sellWalletAccount->debit($this->sell_value, $description, $this->sell_dollar_price);
                    $operatorBuyWalletAccount->credit($this->sell_value, $description, $this->sell_dollar_price);

                    $operatorSellWalletAccount->debit($this->buy_value, $description, $this->buy_dollar_price);
                    $this->buyWalletAccount->credit($this->buy_value, $description, $this->buy_dollar_price);

                    $this->update(['status' => 'completed']);
                });
            });
        });
    }

    /**
     * Cancel ExchangeSwap
     */
    public function cancel(): void
    {
        $this->assertPending();

        $this->update(['status' => 'canceled']);
    }

    /**
     * Swap transfer description
     */
    public function getTransferDescription(): string
    {
        return trans('exchange.swap_description', [
            'sell' => $this->sell_wallet->coin->symbol,
            'buy' => $this->buy_wallet->coin->symbol,
        ]);
    }

    /**
     * Get the channels that model events should broadcast on.
     */
    public function broadcastOn($event): array
    {
        return match ($event) {
            'created' => [$this->sellWalletAccount->user],
            'updated' => [$this->sellWalletAccount->user],
            default => []
        };
    }

    /**
     * Get the data to broadcast for the model.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Related Wallet "buy"
     */
    protected function buyWallet(): Attribute
    {
        return Attribute::get(fn () => $this->buyWalletAccount->wallet);
    }

    /**
     * Related Wallet "sell"
     */
    protected function sellWallet(): Attribute
    {
        return Attribute::get(fn () => $this->sellWalletAccount->wallet);
    }

    /**
     * Related WalletAccount "sell"
     */
    public function sellWalletAccount(): BelongsTo
    {
        return $this->belongsTo(WalletAccount::class, 'sell_wallet_account_id', 'id');
    }

    /**
     * Related WalletAccount "buy"
     */
    public function buyWalletAccount(): BelongsTo
    {
        return $this->belongsTo(WalletAccount::class, 'buy_wallet_account_id', 'id');
    }

    /**
     * Assert that model is pending
     *
     * @throws RuntimeException
     */
    protected function assertPending(): void
    {
        if (!$this->isPending()) {
            throw new LogicException('ExchangeSwap is not pending.');
        }
    }
}
