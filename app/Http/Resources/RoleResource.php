<?php

namespace App\Http\Resources;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Role
 *
 * @property Role $resource
 */
class RoleResource extends JsonResource
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
            'guard_name' => $this->guard_name,
            'order' => $this->order,
            'users_count' => $this->whenCounted('users'),
            'is_administrator' => $this->is_administrator,
            'is_protected' => $this->is_protected,
            'permissions' => PermissionResource::collection($this->permissions),
            'move_up_policy' => $request->user()?->can('moveUp', $this->resource),
            'move_down_policy' => $request->user()?->can('moveDown', $this->resource),
            'update_policy' => $request->user()?->can('update', $this->resource),
            'update_name_policy' => $request->user()?->can('updateName', $this->resource),
            'delete_policy' => $request->user()?->can('delete', $this->resource),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
