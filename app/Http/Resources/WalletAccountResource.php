<?php

namespace App\Http\Resources;

use App\Models\WalletAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin WalletAccount
 */
class WalletAccountResource extends JsonResource
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'balance_on_trade' => $this->balance_on_trade->getValue(),
            'balance_on_trade_price' => $this->balance_on_trade_price,
            'formatted_balance_on_trade_price' => $this->formatted_balance_on_trade_price,
            'balance' => $this->balance->getValue(),
            'balance_price' => $this->balance_price,
            'formatted_balance_price' => $this->formatted_balance_price,
            'available' => $this->available->getValue(),
            'available_price' => $this->available_price,
            'formatted_available_price' => $this->formatted_available_price,
            'total_received' => $this->total_received->getValue(),
            'total_received_price' => $this->total_received_price,
            'formatted_total_received_price' => $this->formatted_total_received_price,
            'total_sent' => $this->total_sent->getValue(),
            'total_sent_price' => $this->total_sent_price,
            'formatted_total_sent_price' => $this->formatted_total_sent_price,
            'coin' => $this->coin,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'wallet' => WalletResource::make($this->whenLoaded('wallet')),
            'user' => UserResource::make($this->whenLoaded('user')),

            $this->mergeWhen(is_numeric($this->available_price_quota), [
                'available_price_quota' => $this->available_price_quota,
            ]),
        ];
    }
}
