<?php

namespace App\Policies;

use App\Models\CommerceCustomer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class CommerceCustomerPolicy
{
    use HandlesAuthorization;

    /**
     * View permission
     */
    public function view(User $user, CommerceCustomer $customer): bool
    {
        return $user->can('manage:commerce') || $user->is($customer->account->user);
    }

    /**
     * Accept new transaction only when there is no pending.
     */
    public function createTransaction(?User $user, CommerceCustomer $customer, Model $subject): bool
    {
        return $customer->activeTransactions($subject)->doesntExist();
    }

    /**
     * Confirm if customer can be deleted
     */
    public function delete(User $user, CommerceCustomer $customer): Response|bool
    {
        if (!$customer->isDeletable()) {
            return $this->deny(trans('commerce.active_customer_transactions'));
        }

        return $this->view($user, $customer);
    }
}
