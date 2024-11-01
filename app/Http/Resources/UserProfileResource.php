<?php

namespace App\Http\Resources;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin UserProfile
 *
 * @property UserProfile $resource
 */
class UserProfileResource extends JsonResource
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
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'full_name' => $this->full_name,
            'bio' => $this->bio,
            'picture' => $this->picture,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            $this->mergeWhen($request->user()?->can('update', $this->resource), [
                'is_complete' => $this->is_complete,
                'dob' => $this->dob,
            ]),
        ];
    }
}
