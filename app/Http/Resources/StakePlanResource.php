<?php

namespace App\Http\Resources;

use App\Models\StakePlan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin StakePlan
 */
class StakePlanResource extends JsonResource
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
            'title' => $this->title,
            'wallet' => WalletResource::make($this->whenLoaded('wallet')),
            'rates' => StakeRateResource::collection($this->whenLoaded('rates')),
            'stakes_count' => $this->whenNotNull($this->stakes_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
