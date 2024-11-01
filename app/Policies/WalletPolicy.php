<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wallet;

class WalletPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view:wallets');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Wallet $wallet): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->viewAny($user) && $user->can('create:wallets');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Wallet $wallet): bool
    {
        return $this->viewAny($user) && $user->can('update:wallets');
    }

    /**
     * Determine whether the user can consolidate wallet.
     */
    public function consolidate(User $user, Wallet $wallet): bool
    {
        return $this->update($user, $wallet) && $wallet->consolidates;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Wallet $wallet): bool
    {
        return $this->viewAny($user) && $user->can('delete:wallets') && $wallet->coin->isDeletable();
    }
}
