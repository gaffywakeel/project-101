<?php

namespace App\Policies;

use App\Models\ExchangeSwap;
use App\Models\User;

class ExchangeSwapPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view:exchange_swaps');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExchangeSwap $exchangeSwap): bool
    {
        return $this->viewAny($user) || $user->is($exchangeSwap->sellWalletAccount->user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExchangeSwap $exchangeSwap): bool
    {
        return $user->can('update:exchange_swaps');
    }

    /**
     * Determine whether the user can approve the ExchangeSwap.
     */
    public function approve(User $user, ExchangeSwap $exchangeSwap): bool
    {
        return $this->update($user, $exchangeSwap) && $exchangeSwap->isPending();
    }

    /**
     * Determine whether the user can cancel the ExchangeSwap.
     */
    public function cancel(User $user, ExchangeSwap $exchangeSwap): bool
    {
        return $this->update($user, $exchangeSwap) && $exchangeSwap->isPending();
    }
}
