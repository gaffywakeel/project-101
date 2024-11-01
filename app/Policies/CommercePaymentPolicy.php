<?php

namespace App\Policies;

use App\Models\CommercePayment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CommercePaymentPolicy
{
    use HandlesAuthorization;

    /**
     * View permission
     */
    public function view(User $user, CommercePayment $payment): bool
    {
        return $user->can('manage:commerce') || $user->is($payment->account->user);
    }

    /**
     * Check if payment is publicly available
     */
    public function viewPage(?User $user, CommercePayment $payment): Response
    {
        if ($payment->isAvailable()) {
            return $this->allow();
        }

        return $this->deny(trans('commerce.payment_unavailable'));
    }

    /**
     * Permission to create commerce transaction
     */
    public function createTransaction(?User $user, CommercePayment $payment): bool
    {
        return $payment->isAvailable();
    }

    /**
     * Check if payment can be updated
     */
    public function update(User $user, CommercePayment $payment): Response|bool
    {
        if ($payment->isThroughApi()) {
            return $this->deny(trans('commerce.cannot_update_payment'));
        }

        return $this->view($user, $payment);
    }

    /**
     * Prevent deleting payment with pending transactions
     */
    public function delete(User $user, CommercePayment $payment): Response|bool
    {
        if (!$payment->isDeletable()) {
            return $this->deny(trans('commerce.cannot_delete_payment'));
        }

        return $this->view($user, $payment);
    }

    /**
     * Authorize status toggle
     */
    public function toggleStatus(User $user, CommercePayment $payment): Response|bool
    {
        if ($payment->expires_at || $payment->isThroughApi()) {
            return $this->deny(trans('commerce.expiring_payment'));
        }

        return $this->view($user, $payment);
    }
}
