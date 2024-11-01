<?php

namespace App\Http\Resources;

use App\Models\StakeRate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin StakeRate
 */
class StakeRateResource extends JsonResource
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
            'days' => $this->days,
            'annual_rate' => $this->annual_rate,
            'rate' => $this->rate,
            'min_value' => $this->min_value,
            'max_value' => $this->max_value,
        ];
    }
}
