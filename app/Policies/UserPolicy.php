<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can follow the model.
     */
    public function follow(User $user, User $model): bool
    {
        return $user->isNot($model);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->is($model) || ($user->superiorTo($model) && $user->can('manage:users'));
    }

    /**
     * Determine whether the user can deactivate the model.
     */
    public function manage(User $user, User $model): bool
    {
        return $user->isNot($model) && $user->superiorTo($model) && $user->can('manage:users');
    }
}
