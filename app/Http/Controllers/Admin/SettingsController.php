<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:settings'));
    }

    /**
     * Get general settings
     *
     * @return array
     */
    public function getGeneral()
    {
        return $this->getSettings([
            'min_payment',
            'max_payment',
            'price_cache',
        ]);
    }

    /**
     * Update general settings
     *
     *
     * @throws ValidationException
     */
    public function updateGeneral(Request $request)
    {
        $validated = $this->validate($request, [
            'min_payment' => 'required|integer|min:1|max_digits:14',
            'max_payment' => 'required|integer|gt:min_payment|max_digits:14',
            'price_cache' => 'required|integer|min:5|max:120',
        ]);

        $this->setSettings($validated);
    }

    /**
     * Get service settings
     *
     * @return array
     */
    public function getService()
    {
        return $this->getSettings([
            'user_setup',
            'enable_mail',
            'enable_database',
            'enable_sms',
        ]);
    }

    /**
     * Update service settings
     *
     *
     * @throws ValidationException
     */
    public function updateService(Request $request)
    {
        $validated = $this->validate($request, [
            'user_setup' => 'required|boolean',
            'enable_mail' => 'required|boolean',
            'enable_database' => 'required|boolean',
            'enable_sms' => 'required|boolean',
        ]);

        $this->setSettings($validated);
    }

    /**
     * Get settings
     */
    protected function getSettings(array $keys): array
    {
        return collect($keys)->mapWithKeys(function ($key) {
            return [$key => settings()->get($key)];
        })->toArray();
    }

    /**
     * Set settings
     */
    protected function setSettings(array $data): void
    {
        foreach ($data as $key => $value) {
            settings()->put($key, $value);
        }
    }
}
