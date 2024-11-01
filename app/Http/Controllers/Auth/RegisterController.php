<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserActivities\Registered;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RedirectsUsers;

    /**
     * Where to redirect users after registration.
     */
    protected string $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Handle a registration request for the application.
     *
     *
     * @throws ValidationException
     * @throws \Throwable
     */
    public function register(StoreUserRequest $request): Response
    {
        $user = $this->create($request->validated());

        $this->guard()->login($user);

        $request->session()->regenerate();

        event(new Registered($user));

        return !$request->wantsJson() ?
            redirect()->to($this->redirectPath()) :
            response()->noContent();
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data): User
    {
        return User::create([
            'name' => Arr::get($data, 'name'),
            'email' => Arr::get($data, 'email'),
            'phone' => Arr::get($data, 'phone'),
            'password' => Hash::make(Arr::get($data, 'password')),
        ]);
    }

    /**
     * Get the guard to be used during registration.
     */
    protected function guard(): StatefulGuard
    {
        return Auth::guard();
    }
}
