<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserActivities\LoggedIn;
use App\Http\Controllers\Controller;
use App\Http\Requests\RecaptchaRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\TwoFactorToken;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use RedirectsUsers, ThrottlesLogins;

    /**
     * The number of minutes to throttle for.
     *
     * @return int
     */
    protected int $decayMinutes = 60;

    /**
     * Where to redirect users after login.
     */
    protected string $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @throws ValidationException
     */
    public function login(RecaptchaRequest $request): Response
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $this->clearLoginAttempts($request);

            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        $this->sendFailedLoginResponse($request);
    }

    /**
     * Login as demo User
     */
    public function demoLogin(Request $request): Response
    {
        Gate::allowIf(config('app.demo'));

        $this->guard()->login(User::demo()->firstOrFail());

        return $this->sendLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     *
     * @throws ValidationException
     */
    protected function validateLogin(Request $request): void
    {
        $request->validate([
            $this->username() => ['required', 'string', 'max:250'],
            'password' => ['required', 'string', 'max:250'],
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     *
     * @throws ValidationException
     */
    protected function attemptLogin(Request $request): bool
    {
        return $this->guard()->attemptWhen(
            $this->credentials($request),
            function (User $user) use ($request) {
                if ($user->isTwoFactorEnabled()) {
                    $request->validate([
                        'token' => [
                            'required', 'string', 'max:100',
                            new TwoFactorToken($user),
                        ],
                    ]);
                }

                if ($user->isDeactivated()) {
                    throw ValidationException::withMessages([
                        $this->username() => trans('auth.deactivated', [
                            'date' => $user->deactivated_until->toJSON(),
                        ]),
                    ]);
                }

                return true;
            },
            $request->boolean('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     */
    protected function credentials(Request $request): array
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     */
    protected function sendLoginResponse(Request $request): Response
    {
        $request->session()->regenerate();

        $user = $this->guard()->user();

        $user->update(['last_login_at' => now()]);

        event(new LoggedIn($user));

        $url = session()->pull('url.intended');

        return !$request->wantsJson()
            ? redirect()->to($url ?: $this->redirectPath())
            : response()->noContent();
    }

    /**
     * Get the failed login response instance.
     *
     *
     * @throws ValidationException
     */
    protected function sendFailedLoginResponse(Request $request): void
    {
        throw ValidationException::withMessages([
            $this->username() => trans('auth.failed'),
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     */
    protected function username(): string
    {
        return config('auth.credential', 'email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): Response
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return !$request->wantsJson()
            ? redirect()->to($this->redirectPath())
            : response()->noContent();
    }

    /**
     * Get the guard to be used during authentication.
     */
    protected function guard(): StatefulGuard
    {
        return Auth::guard('web');
    }
}
