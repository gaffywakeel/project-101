<?php

namespace App\Http\Resources;

use App\Models\PeerOffer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PeerOffer
 */
class PeerOfferResource extends JsonResource
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
            'type' => $this->type,
            'currency' => $this->currency,
            'country' => $this->country,
            'min_amount' => $this->min_amount->getValue(),
            'formatted_min_amount' => $this->formatted_min_amount,
            'min_value' => $this->min_value->getValue(),
            'max_amount' => $this->max_amount->getValue(),
            'formatted_max_amount' => $this->formatted_max_amount,
            'max_value' => $this->max_value->getValue(),
            'price_type' => $this->price_type,
            'price' => $this->price->getValue(),
            'formatted_price' => $this->formatted_price,
            'time_limit' => $this->time_limit,
            'instruction' => $this->instruction,
            'auto_reply' => $this->auto_reply,
            'require_long_term' => $this->require_long_term,
            'require_verification' => $this->require_verification,
            'require_following' => $this->require_following,
            'display' => $this->display,
            'status' => $this->status,
            'closed_at' => $this->closed_at,
            'payment' => $this->payment,
            'coin' => CoinResource::make($this->whenAppended('coin')),
            'payment_method' => PeerPaymentMethodResource::make($this->whenLoaded('paymentMethod')),
            'owner' => UserResource::make($this->whenAppended('owner')),
            'updatable' => $this->isManagedBy($request->user()),
            'tradable' => $this->canTradeWith($request->user()),

            $this->mergeWhen($this->isManagedBy($request->user()), [
                'wallet_account' => WalletAccountResource::make($this->whenLoaded('walletAccount')),
                'bank_account' => BankAccountResource::make($this->whenLoaded('bankAccount')),
            ]),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
