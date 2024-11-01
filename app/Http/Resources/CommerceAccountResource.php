<?php

namespace App\Http\Resources;

use App\Models\CommerceAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CommerceAccount
 */
class CommerceAccountResource extends JsonResource
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
            'name' => $this->name,
            'website' => $this->website,
            'email' => $this->email,
            'phone' => $this->phone,
            'about' => $this->about,
            'logo' => $this->logo,
            'transaction_interval' => $this->transaction_interval,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
