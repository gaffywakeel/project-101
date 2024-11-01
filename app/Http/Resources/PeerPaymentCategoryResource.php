<?php

namespace App\Http\Resources;

use App\Models\PeerPaymentCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PeerPaymentCategory
 */
class PeerPaymentCategoryResource extends JsonResource
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
            'description' => $this->description,
            'methods_count' => $this->whenNotNull($this->methods_count),
            'logo' => $this->logo,
        ];
    }
}
