<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserActivities\UpdatedProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\VerifiedRequest;
use App\Http\Resources\UserActivityResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:users'));
    }

    /**
     * Paginate users
     */
    public function paginate(): AnonymousResourceCollection
    {
        return UserResource::collection(paginate(User::query()->latest()));
    }

    /**
     * Paginate activities
     */
    public function activityPaginate(User $user): AnonymousResourceCollection
    {
        $activities = paginate($user->activities()->latest());

        return UserActivityResource::collection($activities);
    }

    /**
     * Reset two factor secret
     */
    public function resetTwoFactor(User $user): void
    {
        $user->acquireLock(function (User $user) {
            $user->resetTwoFactorSecret();
        });
    }

    /**
     * Disable two factor
     */
    public function disableTwoFactor(User $user): void
    {
        $user->acquireLock(function (User $user) {
            if ($user->isTwoFactorEnabled()) {
                $user->disableTwoFactor();
            }
        });
    }

    /**
     * Reset user's password
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function resetPassword(VerifiedRequest $request, User $user)
    {
        $this->validate($request, [
            'new_password' => ['required', 'string', 'min:8', 'max:255', 'confirmed', Password::defaults()],
        ]);

        $password = Hash::make($request->get('new_password'));

        $user->update(['password' => $password]);
    }

    /**
     * Update user's profile
     */
    public function update(Request $request, User $user)
    {
        $user->acquireLock(function (User $user) use ($request) {
            $user->fill($this->validate($request, [
                'country' => ['required', Rule::in($this->getCountryCodes())],
            ]));

            $user->profile->fill($this->validate($request, [
                'last_name' => ['required', 'string', 'max:100'],
                'first_name' => ['required', 'string', 'max:100'],
            ]));

            tap($user)->save()->profile->save();
            event(new UpdatedProfile($user));
        });
    }

    /**
     * Deactivate user
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deactivate(Request $request, User $user)
    {
        $this->authorize('manage', $user);

        $validated = $this->validate($request, [
            'date' => 'required|date|after:now',
        ]);

        $user->update(['deactivated_until' => $validated['date']]);
    }

    /**
     * Activate user
     */
    public function activate(User $user)
    {
        $this->authorize('manage', $user);

        $user->update(['deactivated_until' => null]);
    }

    /**
     * Batch deactivate
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function batchDeactivate(Request $request)
    {
        $validated = $this->validate($request, [
            'date' => 'required|date|after:now',
            'users' => 'required|array',
            'users.*' => 'required|exists:users,id',
        ]);

        Auth::user()->subordinates()->whereIn('id', $validated['users'])
            ->update(['deactivated_until' => $validated['date']]);
    }

    /**
     * Batch activate
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function batchActivate(Request $request)
    {
        $validated = $this->validate($request, [
            'users' => 'required|array',
            'users.*' => 'required|exists:users,id',
        ]);

        Auth::user()->subordinates()->whereIn('id', $validated['users'])
            ->update(['deactivated_until' => null]);
    }

    /**
     * Get country codes
     */
    protected function getCountryCodes(): Collection
    {
        return collect(config('countries'))->keys();
    }
}
