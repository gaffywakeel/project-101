<?php

namespace App\Http\Resources;

use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PaymentTransaction
 */
class PaymentTransactionResource extends JsonResource
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
            'value' => $this->value->getValue(),
            'formatted_value' => $this->formatted_value,
            'balance' => $this->balance->getValue(),
            'formatted_balance' => $this->formatted_balance,
            'description' => $this->description,
            'gateway_ref' => $this->gateway_ref,
            'gateway_name' => $this->gateway_name,
            'gateway_url' => $this->gateway_url,
            'transfer_bank' => $this->transfer_bank,
            'transfer_beneficiary' => $this->transfer_beneficiary,
            'transfer_number' => $this->transfer_number,
            'transfer_country' => $this->transfer_country,
            'transfer_note' => $this->transfer_note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            $this->mergeWhen($request->user()?->can('manage:payments'), [
                'account' => PaymentAccountResource::make($this->whenLoaded('account')),
            ]),
        ];
    }
}
