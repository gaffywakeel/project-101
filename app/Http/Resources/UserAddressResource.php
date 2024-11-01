<?php

namespace App\Http\Resources;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin UserAddress
 */
class UserAddressResource extends JsonResource
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
            'address' => $this->address,
            'unit' => $this->unit,
            'city' => $this->city,
            'postcode' => $this->postcode,
            'state' => $this->state,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
