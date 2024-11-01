<?php

namespace App\Http\Controllers;

use App\Helpers\LocaleManager;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Models\Module;
use App\Models\Permission;
use ArrayObject;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AppController extends Controller
{
    /**
     * Locale manager
     */
    protected LocaleManager $localeManager;

    /**
     * AppController constructor.
     */
    public function __construct(LocaleManager $localeManager)
    {
        $this->localeManager = $localeManager;
    }

    /**
     * Show admin dashboard.
     */
    public function admin(): View
    {
        return view('admin', [
            'data' => $this->context(),
        ]);
    }

    /**
     * Authentication page.
     */
    public function auth(): View
    {
        return view('auth', [
            'data' => $this->context(),
        ]);
    }

    /**
     * Where it all begins!
     */
    public function index(): View
    {
        return view('index', [
            'data' => $this->context(),
        ]);
    }

    /**
     * Get IP Info
     */
    public function ipInfo(Request $request): array
    {
        return geoip($request->ip())->toArray();
    }

    /**
     * Get app data
     */
    protected function context(): array
    {
        return [
            'name' => config('app.name'),
            'settings' => [
                'demo' => config('app.demo', false),
                'locales' => $this->getLocales(),
                'currency' => $this->getBaseCurrency(),
                'modules' => $this->getModules(),
                'theme' => settings()->theme->all(),
                'brand' => settings()->brand->all(),
                'recaptcha' => [
                    'enable' => config('services.recaptcha.enable'),
                    'sitekey' => config('services.recaptcha.sitekey'),
                    'size' => config('services.recaptcha.size'),
                ],
            ],
            'auth' => [
                'user' => $this->getAuthUser(),
                'credential' => config('auth.credential', 'email'),
                'setup' => settings()->get('user_setup'),
                'permissions' => $this->getPermissions(),
            ],
            'broadcast' => getBroadcastConfig(),
            'notification' => session('notification'),
            'csrfToken' => csrf_token(),
        ];
    }

    /**
     * Get user object
     */
    protected function getAuthUser(): UserResource
    {
        Auth::user()?->updatePresence('online');

        return UserResource::make(Auth::user());
    }

    /**
     * Get supported locales object
     */
    protected function getLocales(): ArrayObject
    {
        $locales = $this->localeManager->getLocales();

        return new ArrayObject($locales->toArray());
    }

    /**
     * Get modules
     */
    protected function getModules(): Collection
    {
        return Module::all()->pluck('status', 'name');
    }

    /**
     * Get permission names
     */
    protected function getPermissions(): AnonymousResourceCollection
    {
        $permissions = Permission::orderBy('order')->get();

        return PermissionResource::collection($permissions);
    }

    /**
     * Get base currency
     */
    protected function getBaseCurrency(): string
    {
        return app('exchanger')->config('base_currency');
    }
}
