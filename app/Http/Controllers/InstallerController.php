<?php

namespace App\Http\Controllers;

use App\Helpers\Settings;
use App\Models\Role;
use App\Models\User;
use App\Rules\Username;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InstallerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('installer.protect');
    }

    /**
     * Show installer
     */
    public function view(Settings $settings): View
    {
        $data = $this->getData($settings);

        return view('installer', compact('data', 'settings'));
    }

    /**
     * Get installer settings
     */
    protected function getData(Settings $settings): array
    {
        return [
            'name' => config('app.name'),
            'settings' => [
                'theme' => [
                    'mode' => $settings->theme->get('mode'),
                    'direction' => $settings->theme->get('direction'),
                    'color' => $settings->theme->get('color'),
                ],
                'installer' => [
                    'license' => app('installer')->hasLicenseCode(),
                ],
            ],
            'notification' => session('notification'),
            'csrfToken' => csrf_token(),
        ];
    }

    /**
     * Install license code
     *
     *
     * @throws ValidationException
     */
    public function install(Request $request): mixed
    {
        $this->validate($request, [
            'code' => 'required|uuid',
        ]);

        $code = $request->get('code');

        try {
            return app('installer')->setLicenseCode($code);
        } catch (RequestException $e) {
            $errors = $e->response->json('errors.code');

            if (!is_array($errors)) {
                abort(403, $e->response->json('message'));
            } else {
                abort(403, Arr::first($errors));
            }
        }
    }

    /**
     * Register root account
     *
     * @throws ValidationException
     */
    public function register(Request $request): void
    {
        if (User::administrator()->exists()) {
            abort(403, trans('auth.action_forbidden'));
        }

        $this->validate($request, [
            'name' => ['required', 'string', 'min:3', 'max:25', 'unique:users', new Username],
            'email' => ['required', 'string', 'email:rfc,dns,spoof', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed', Password::defaults()],
        ]);

        $this->createUser($request->all());
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function createUser(array $data): User
    {
        $user = User::create([
            'email' => $data['email'],
            'name' => $data['name'],
            'password' => Hash::make($data['password']),
        ]);

        return tap($user, function (User $user) {
            $user->assignRole(Role::administrator());
            $user->assignRole(Role::operator());
        });
    }
}
