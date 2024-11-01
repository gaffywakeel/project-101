<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view:roles');
    }

    /**
     * Determine whether the user can assign roles to others.
     */
    public function assign(User $user): bool
    {
        return $user->can('assign:roles');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create:roles');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        if (Role::administrator()->is($role)) {
            return false;
        }

        return $this->view($user, $role) && $user->can('update:roles');
    }

    /**
     * Determine whether the user can update the Role name.
     */
    public function updateName(User $user, Role $role): bool
    {
        return $this->update($user, $role) && !$role->is_protected;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        return $this->view($user, $role) && $user->can('delete:roles') && !$role->is_protected;
    }

    /**
     * Determine whether the user can move Role up the hierarchy.
     */
    public function moveUp(User $user, Role $role): bool
    {
        return $this->update($user, $role) && $role->previous()?->isNot(Role::administrator());
    }

    /**
     * Determine whether the user can move Role down the hierarchy.
     */
    public function moveDown(User $user, Role $role): bool
    {
        return $this->update($user, $role) && $role->next();
    }
}
