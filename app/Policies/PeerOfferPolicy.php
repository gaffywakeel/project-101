<?php

namespace App\Policies;

use App\Models\PeerOffer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PeerOfferPolicy
{
    use HandlesAuthorization;

    /**
     * Can enable offer
     */
    public function enable(User $user, PeerOffer $offer): bool
    {
        return $offer->canEnableBy($user);
    }

    /**
     * Can disable offer
     */
    public function disable(User $user, PeerOffer $offer): bool
    {
        return $offer->canDisableBy($user);
    }

    /**
     * Can close offer
     */
    public function close(User $user, PeerOffer $offer): bool
    {
        return $offer->canCloseBy($user);
    }

    /**
     * Can trade with offer
     */
    public function tradable(User $user, PeerOffer $offer): bool
    {
        return $offer->canTradeWith($user);
    }
}
