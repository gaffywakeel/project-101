<?php

namespace App\Http\Controllers;

use App\Events\UserActivities\EnabledTwoFactor;
use App\Events\UserActivities\PasswordChanged;
use App\Events\UserActivities\UpdatedPicture;
use App\Events\UserActivities\UpdatedPreference;
use App\Events\UserActivities\UpdatedProfile;
use App\Events\UserActivities\VerifiedEmail;
use App\Events\UserActivities\VerifiedPhone;
use App\Http\Resources\UserActivityResource;
use App\Http\Resources\UserNotificationSettingResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Rules\Guarded;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
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
     * Get user data
     */
    public function get(): UserResource
    {
        return UserResource::make(Auth::user());
    }

    /**
     * Get notification settings
     */
    public function getNotificationSettings(): AnonymousResourceCollection
    {
        return UserNotificationSettingResource::collection(Auth::user()->getNotificationSettings());
    }

    /**
     * Update notification settings
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function updateNotificationSettings(Request $request)
    {
        $validated = $this->validate($request, [
            'notification' => 'required|array',
            'notification.*' => 'required|array:email,database,sms',
            'notification.*.*' => 'required|boolean',
        ]);

        foreach ($validated['notification'] as $name => $properties) {
            Auth::user()->notificationSettings()->whereName($name)
                ->firstOrFail()->update($properties);
        }

        event(new UpdatedPreference(Auth::user()));
    }

    /**
     * Paginate user activites
     */
    public function activityPaginate(): AnonymousResourceCollection
    {
        $activities = paginate(Auth::user()->activities()->latest());

        return UserActivityResource::collection($activities);
    }

    /**
     * Get two factor secret
     *
     *
     * @throws ValidationException
     */
    public function getTwoFactor(Request $request): JsonResponse
    {
        if (Auth::user()->isTwoFactorEnabled()) {
            $this->validate($request, [
                'token' => [
                    'required', function ($attribute, $value, $fail) {
                        if (!Auth::user()->verifyTwoFactorToken($value)) {
                            $fail(trans('two-factor.invalid_token'));
                        }
                    },
                ],
            ]);
        }

        return response()->json([
            'secret' => Auth::user()->two_factor_secret,
            'url' => Auth::user()->getTwoFactorUrl(),
        ]);
    }

    /**
     * Reset two factor secret
     */
    public function resetTwoFactor(): JsonResponse
    {
        return Auth::user()->acquireLock(function (User $user) {
            if ($user->isTwoFactorEnabled()) {
                abort(403, trans('two-factor.enabled'));
            }

            $user->resetTwoFactorSecret();

            return response()->json([
                'secret' => $user->two_factor_secret,
                'url' => $user->getTwoFactorUrl(),
            ]);
        });
    }

    /**
     * Enable two factor
     *
     *
     * @throws ValidationException
     */
    public function setTwoFactor(Request $request)
    {
        $this->validate($request, [
            'token' => [
                'required', function ($attribute, $value, $fail) {
                    if (!Auth::user()->verifyTwoFactorToken($value)) {
                        $fail(trans('two-factor.invalid_token'));
                    }
                },
            ],
        ]);

        Auth::user()->enableTwoFactor();

        event(new EnabledTwoFactor(Auth::user()));
    }

    /**
     * Update profile
     *
     *
     * @throws ValidationException
     */
    public function update(Request $request)
    {
        Auth::user()->acquireLock(function (User $user) use ($request) {
            $profile = $user->profile;

            $user->fill($this->validate($request, [
                'email' => ['email:rfc,dns,spoof', 'max:255', $user->getUniqueRule()],
                'phone' => [Rule::phone()->international(), $user->getUniqueRule()],
                'country' => [Rule::in($this->getCountryCodes()), new Guarded($user->country)],
                'currency' => ['string', 'max:5', 'exists:supported_currencies,code'],
            ]));

            $user->profile->fill($this->validate($request, [
                'dob' => ['date', 'before:-18 years'],
                'first_name' => ['string', 'max:100', new Guarded($profile->first_name)],
                'last_name' => ['string', 'max:100', new Guarded($profile->last_name)],
                'bio' => ['nullable', 'string', 'max:1000'],
            ]));

            tap($user)->save()->profile->save();
            event(new UpdatedProfile($user));
        });
    }

    /**
     * Get country codes
     */
    protected function getCountryCodes(): Collection
    {
        return collect(config('countries'))->keys();
    }

    /**
     * Verify phone with token
     *
     *
     * @throws ValidationException
     */
    public function verifyPhoneWithToken(Request $request)
    {
        if (Auth::user()->isPhoneVerified()) {
            abort(403, trans('verification.phone_already_verified'));
        }

        $validated = $this->validate($request, [
            'token' => 'required|string|min:6|max:10',
        ]);

        if (Auth::user()->validatePhoneToken($validated['token'])) {
            Auth::user()->update(['phone_verified_at' => now()]);
        } else {
            abort(422, trans('verification.invalid_phone_token'));
        }

        event(new VerifiedPhone(Auth::user()));
    }

    /**
     * Verify email with token
     *
     *
     * @throws ValidationException
     */
    public function verifyEmailWithToken(Request $request)
    {
        if (Auth::user()->isEmailVerified()) {
            abort(403, trans('verification.email_already_verified'));
        }

        $validated = $this->validate($request, [
            'token' => 'required|string|min:6|max:10',
        ]);

        if (Auth::user()->validateEmailToken($validated['token'])) {
            Auth::user()->update(['email_verified_at' => now()]);
        } else {
            abort(422, trans('verification.invalid_email_token'));
        }

        event(new VerifiedEmail(Auth::user()));
    }

    /**
     * Upload Picture
     *
     *
     * @throws ValidationException
     */
    public function uploadPicture(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimetypes:image/png,image/jpeg|dimensions:ratio=1|file|max:100',
        ]);

        $file = $request->file('file');

        $picture = savePublicFile($file, Auth::user()->path());
        Auth::user()->profile->update(['picture' => $picture]);

        event(new UpdatedPicture(Auth::user()));
    }

    /**
     * Change user password
     *
     *
     * @throws ValidationException|AuthenticationException
     */
    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => [
                Rule::requiredIf(true), 'max:255',
                'current_password',
            ],

            'password' => [
                'required', 'max:255', 'confirmed',
                'different:old_password', Password::defaults(),
            ],
        ]);

        $password = $request->get('password');
        Auth::user()->update(['password' => Hash::make($password)]);
        event(new PasswordChanged(Auth::user()));

        Auth::logoutOtherDevices($password);
    }

    /**
     * Update presence as online
     */
    public function setOnline()
    {
        Auth::user()->updatePresence('online');
    }

    /**
     * Update presence as away
     */
    public function setAway()
    {
        Auth::user()->updatePresence('away');
    }

    /**
     * Update presence as offline
     */
    public function setOffline()
    {
        Auth::user()->updatePresence('offline');
    }
}
