<?php

namespace App\Http\Resources;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Bank
 */
class BankResource extends JsonResource
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
            'logo' => $this->logo,
            'operating_countries' => OperatingCountryResource::collection($this->whenLoaded('operatingCountries')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
