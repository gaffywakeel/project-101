<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ThemeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:customization'));
    }

    /**
     * Set theme mode
     *
     *
     * @throws ValidationException
     */
    public function setMode(Request $request)
    {
        $validated = $this->validate($request, ['value' => 'required|in:light,dark']);
        settings()->theme->put('mode', $validated['value']);
    }

    /**
     * Set theme direction
     *
     *
     * @throws ValidationException
     */
    public function setDirection(Request $request)
    {
        $validated = $this->validate($request, ['value' => 'required|in:ltr,rtl']);
        settings()->theme->put('direction', $validated['value']);
    }

    /**
     * Set theme color
     *
     *
     * @throws ValidationException
     */
    public function setColor(Request $request)
    {
        $validated = $this->validate($request, ['value' => 'required|string|max:50']);
        settings()->theme->put('color', $validated['value']);
    }
}
