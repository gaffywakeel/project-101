<?php

namespace App\Http\Resources;

use App\Models\Coin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Coin
 */
class CoinResource extends JsonResource
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
            'identifier' => $this->identifier,
            'base_unit' => $this->base_unit,
            'precision' => $this->precision,
            'currency_precision' => $this->currency_precision,
            'symbol' => $this->symbol,
            'color' => $this->color,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'svg_icon' => $this->svg_icon,
        ];
    }
}
