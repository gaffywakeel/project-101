<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserActivities\PasswordReset;
use App\Http\Controllers\Controller;
use App\Http\Requests\RecaptchaRequest;
use App\Models\User;
use App\Notifications\Auth\EmailToken;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * The number of seconds to throttle for.
     */
    public int $decaySeconds = 600;

    /**
     * The maximum attempts for email code
     */
    public int $maxAttempts = 5;

    /**
     * Where to redirect users after resetting their password.
     */
    protected string $redirectTo = RouteServiceProvider::HOME;

    /**
     * Reset the given user's password.
     *
     * @param  CanResetPassword|User  $user
     */
    protected function resetPassword($user, $password): void
    {
        $this->setUserPassword($user, $password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

        $this->guard()->login($user);
    }

    /**
     * Send email token
     */
    public function sendEmailCode(RecaptchaRequest $request): Response
    {
        $request->validate([
            'email' => 'required|email|max:250',
        ]);

        $user = $this->retrieveUser($request);

        if ($user instanceof User) {
            RateLimiter::attempt(
                "recovery:$user->email",
                $this->maxAttempts,
                function () use ($user) {
                    return $user->notify(new EmailToken());
                },
                $this->decaySeconds
            );
        }

        return response()->noContent();
    }

    /**
     * Request broker token
     *
     * @throws ValidationException
     */
    public function requestToken(Request $request): JsonResponse
    {
        $this->validate($request, [
            'code' => 'required|string|min:6|max:10',
            'email' => 'required|email|max:250',
        ]);

        $user = $this->retrieveUser($request);
        $code = $request->get('code');

        if (!$user instanceof User || !$user->validateEmailToken($code)) {
            abort(403, trans('auth.invalid_token'));
        }

        return response()->json([
            'token' => $this->createToken($user),
        ]);
    }

    /**
     * Find user by email
     */
    protected function retrieveUser(Request $request): ?Authenticatable
    {
        return $this->guard()->getProvider()->retrieveByCredentials($request->only('email'));
    }

    /**
     * Create broker token
     */
    protected function createToken(User $user): string
    {
        return $this->broker()->createToken($user);
    }
}
