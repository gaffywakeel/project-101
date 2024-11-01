<?php

namespace App\Http\Resources;

use App\Models\FeatureLimit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FeatureLimit
 */
class FeatureLimitResource extends JsonResource
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
            'name' => $this->name,
            'title' => $this->title,
            'unverified_limit' => $this->unverified_limit,
            'basic_limit' => $this->basic_limit,
            'advanced_limit' => $this->advanced_limit,
            'type' => $this->type,
            'period' => $this->period,
            'usage' => $this->getUsage($request->user()),
            'available' => $this->getAvailable($request->user()),
            'limit' => $this->getLimit($request->user()),
        ];
    }
}
