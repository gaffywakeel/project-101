<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 *
 * @property User $resource
 */
class UserResource extends JsonResource
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
            'presence' => $this->presence,
            'followers_count' => $this->whenNotNull($this->followers_count),
            'following_count' => $this->whenNotNull($this->following_count),
            'profile' => UserProfileResource::make($this->whenLoaded('profile')),
            'last_seen_at' => $this->last_seen_at,
            'last_login_at' => $this->last_login_at,
            'currency' => $this->currency,
            'currency_name' => $this->currency_name,
            'country' => $this->country,
            'country_name' => $this->country_name,
            'average_rating' => $this->average_rating,
            'sum_rating' => $this->sum_rating,
            'total_rating' => $this->total_rating,
            'active' => $this->active,
            'long_term' => $this->long_term,
            'phone_verified_at' => $this->phone_verified_at,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            $this->mergeWhen($request->user()?->can('update', $this->resource), [
                'email' => $this->email,
                'phone' => $this->phone,
                'two_factor_enable' => $this->two_factor_enable,
                'country_operation' => $this->country_operation,
                'deactivated_until' => $this->deactivated_until,
                'notifications_read_at' => $this->notifications_read_at,
                'location' => $this->location,
                'is_administrator' => $this->is_administrator,
                'all_permissions' => $this->all_permissions,
                'all_roles' => $this->all_roles,
            ]),

            $this->mergeWhen($request->user()?->isNot($this->resource), [
                'following' => $request->user()?->getFollowingPivot($this->resource),
            ]),

            'follow_policy' => $request->user()?->can('follow', $this->resource),
            'manage_policy' => $request->user()?->can('manage', $this->resource),
            'update_policy' => $request->user()?->can('update', $this->resource),
        ];
    }
}
