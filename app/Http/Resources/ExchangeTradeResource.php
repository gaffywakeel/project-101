<?php

namespace App\Http\Resources;

use App\Models\ExchangeTrade;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ExchangeTrade
 */
class ExchangeTradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        if (is_null($this->resource)) {
            return parent::toArray($request);
        }

        return [
            'id' => $this->id,
            'status' => $this->status,
            'type' => $this->type,
            'payment_value' => $this->payment_value->getValue(),
            'formatted_payment_value' => $this->formatted_payment_value,
            'wallet_value' => $this->wallet_value->getValue(),
            'wallet_value_price' => $this->wallet_value_price,
            'formatted_wallet_value_price' => $this->formatted_wallet_value_price,
            'dollar_price' => $this->dollar_price,
            'completed_at' => $this->completed_at,
            'coin' => CoinResource::make($this->whenAppended('coin')),
            'trader' => UserResource::make($this->whenLoaded('trader')),
            'payment_currency' => $this->payment_currency,
            'payment_symbol' => $this->payment_symbol,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            $this->mergeWhen($request->user()?->can('manage:exchange'), [
                'wallet_account' => WalletAccountResource::make($this->whenLoaded('walletAccount')),
            ]),
        ];
    }
}
