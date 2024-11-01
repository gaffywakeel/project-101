<?php

namespace App\Http\Controllers;

use App\Http\Resources\PeerOfferResource;
use App\Http\Resources\RatingResource;
use App\Http\Resources\UserResource;
use App\Models\PeerOffer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    /**
     * Get user by name
     */
    public function get(User $user): UserResource
    {
        return UserResource::make($user->loadCount(['followers', 'following']));
    }

    /**
     * Follow user
     *
     * @return void
     */
    public function follow(User $user)
    {
        $this->authorize('follow', $user);

        Auth::user()->following()->sync($user, false);
    }

    /**
     * Unfollow user
     *
     * @return void
     */
    public function unfollow(User $user)
    {
        $this->authorize('follow', $user);

        Auth::user()->following()->detach($user);
    }

    /**
     * Paginate followers
     */
    public function followersPaginate(User $user): AnonymousResourceCollection
    {
        $records = paginate($user->followers());

        return UserResource::collection($records);
    }

    /**
     * Paginate following
     */
    public function followingPaginate(User $user): AnonymousResourceCollection
    {
        $records = paginate($user->following());

        return UserResource::collection($records);
    }

    /**
     * Paginate reviews
     */
    public function reviewsPaginate(User $user): AnonymousResourceCollection
    {
        $records = paginate($user->ratings()->whereNotNull('comment'));

        return RatingResource::collection($records);
    }

    /**
     * Get paginated PeerOffers created by user
     */
    public function peerOfferPaginate(User $user, Request $request): AnonymousResourceCollection
    {
        $type = $request->query('type', 'buy');

        $query = PeerOffer::whereType($type)->ownedBy($user);

        if (Auth::user()->isNot($user) && Auth::user()->cannot('manage:peer_trades')) {
            $query = $query->displayedFor(Auth::user());
        }

        return PeerOfferResource::collection(paginate($query));
    }
}
