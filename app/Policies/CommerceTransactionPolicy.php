<?php

namespace App\Policies;

use App\Models\CommerceTransaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommerceTransactionPolicy
{
    use HandlesAuthorization;

    /**
     * View permission
     */
    public function view(User $user, CommerceTransaction $transaction): bool
    {
        return $user->can('manage:commerce') || $user->is($transaction->account->user);
    }
}
