<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserProfile;

class UserProfilePolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserProfile $userProfile): bool
    {
        return $user->id === $userProfile->user_id || ($user->superiorTo($userProfile->user_id) && $user->can('manage:users'));
    }
}
