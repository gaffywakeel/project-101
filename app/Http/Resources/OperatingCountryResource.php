<?php

namespace App\Http\Resources;

use App\Models\OperatingCountry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin OperatingCountry
 */
class OperatingCountryResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            $this->mergeWhen($request->user()?->can('manage:banks'), [
                'banks_count' => $this->whenNotNull($this->banks_count),
            ]),
        ];
    }
}
