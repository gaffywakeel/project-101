<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Paginated records
     */
    public function paginate(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Role::class);

        $query = Role::orderBy('order')->with('permissions')->withCount('users');

        return RoleResource::collection($query->autoPaginate());
    }

    /**
     * Create Role model.
     */
    public function store(Request $request): RoleResource
    {
        $this->authorize('create', Role::class);

        $validated = $request->validate([
            'name' => ['required', 'max:100', Role::uniqueRule()],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'array:name,value'],
            'permissions.*.name' => ['required', Rule::exists(Permission::class)],
            'permissions.*.value' => ['required', 'boolean'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $permissions = collect($validated['permissions'])
            ->pluck('value', 'name')->filter()->keys();

        $role->syncPermissions($permissions->toArray());

        return RoleResource::make($role);
    }

    /**
     * Update Role model
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $updateNamePolicy = $request->user()->can('updateName', $role);

        $validated = $request->validate([
            'name' => [
                Rule::requiredIf($updateNamePolicy),
                'max:100', $role->getUniqueRule(),
            ],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'array:name,value'],
            'permissions.*.name' => ['required', Rule::exists(Permission::class)],
            'permissions.*.value' => ['required', 'boolean'],
        ]);

        if ($updateNamePolicy) {
            $role->name = $validated['name'];
        }

        $permissions = collect($validated['permissions'])
            ->pluck('value', 'name')->filter()->keys();

        $role->syncPermissions($permissions->toArray());

        $role->save();

        return RoleResource::make($role);
    }

    /**
     * Move Role up the hierarchy
     */
    public function moveUp(Role $role): Response
    {
        $this->authorize('moveUp', $role);

        $previous = $role->previous();

        DB::transaction(function () use ($previous, $role) {
            $previousOrder = $previous->order;
            $previous->update(['order' => $role->order]);
            $role->update(['order' => $previousOrder]);
        });

        return response()->noContent();
    }

    /**
     * Move Role down the hierarchy
     */
    public function moveDown(Role $role): Response
    {
        $this->authorize('moveDown', $role);

        $next = $role->next();

        DB::transaction(function () use ($next, $role) {
            $nextOrder = $next->order;
            $next->update(['order' => $role->order]);
            $role->update(['order' => $nextOrder]);
        });

        return response()->noContent();
    }

    /**
     * Delete Role
     */
    public function destroy(Role $role): Response
    {
        $this->authorize('delete', $role);

        $role->delete();

        return response()->noContent();
    }

    /**
     * Assign roles to user
     */
    public function assign(Request $request): Response
    {
        $this->authorize('assign', Role::class);

        $validated = $request->validate([
            'roles' => ['required', 'array', 'max:3'],
            'roles.*' => ['required', Rule::exists(Role::class, 'name')],
            'user' => ['required', Rule::exists(User::class, 'id')],
        ]);

        $user = User::findOrFail($validated['user']);

        $this->authorize('manage', $user);

        $user->syncRoles($validated['roles']);

        return response()->noContent();
    }
}
