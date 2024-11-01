<?php

namespace App\Http\Resources;

use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin UserActivity
 */
class UserActivityResource extends JsonResource
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
            'action' => $this->action,
            'source' => $this->source,
            'agent' => $this->agent,
            'parsed_agent' => $this->parsed_agent,
            'ip' => $this->ip,
            'location' => $this->location,
            'created_at' => $this->created_at,
        ];
    }
}
