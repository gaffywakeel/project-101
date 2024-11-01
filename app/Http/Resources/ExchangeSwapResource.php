<?php

namespace App\Http\Resources;

use App\Models\ExchangeSwap;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ExchangeSwap
 *
 * @property ExchangeSwap $resource
 */
class ExchangeSwapResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (is_null($this->resource)) {
            return parent::toArray($request);
        }

        return [
            'id' => $this->id,
            'sell_value' => $this->sell_value->getValue(),
            'formatted_sell_value_price' => $this->formatted_sell_value_price,
            'sell_value_price' => $this->sell_value_price,
            'sell_wallet' => WalletResource::make($this->whenAppended('sell_wallet')),
            'buy_value' => $this->buy_value->getValue(),
            'formatted_buy_value_price' => $this->formatted_buy_value_price,
            'buy_value_price' => $this->buy_value_price,
            'buy_wallet' => WalletResource::make($this->whenAppended('buy_wallet')),
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            $this->mergeWhen($request->user()?->can('view', $this->resource), [
                'sell_wallet_account' => WalletAccountResource::make($this->whenLoaded('sellWalletAccount')),
                'buy_wallet_account' => WalletAccountResource::make($this->whenLoaded('buyWalletAccount')),
            ]),

            'approve_policy' => $request->user()?->can('approve', $this->resource),
            'cancel_policy' => $request->user()?->can('cancel', $this->resource),
        ];
    }
}
