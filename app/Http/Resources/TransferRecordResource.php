<?php

namespace App\Http\Resources;

use App\Models\TransferRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TransferRecord
 */
class TransferRecordResource extends JsonResource
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
            'value' => $this->value->getValue(),
            'value_price' => $this->value_price,
            'formatted_value_price' => $this->formatted_value_price,
            'balance' => $this->balance->getValue(),
            'balance_price' => $this->balance_price,
            'formatted_balance_price' => $this->formatted_balance_price,
            'coin' => CoinResource::make($this->whenAppended('coin')),
            'confirmed' => $this->confirmed,
            'hash' => $this->hash,
            'description' => $this->description,
            'address' => $this->address,
            'confirmations' => $this->confirmations,
            'required_confirmations' => $this->required_confirmations,
            'dollar_price' => $this->dollar_price,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            $this->mergeWhen($request->user()?->can('manage:wallets'), [
                'wallet_account' => WalletAccountResource::make($this->whenLoaded('walletAccount')),
                'removable' => $this->removable,
            ]),
        ];
    }
}
