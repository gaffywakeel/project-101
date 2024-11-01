<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectInstaller
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldDisplay()) {
            return $next($request);
        }

        if ($request->wantsJson()) {
            return abort(403, 'Installation already completed.');
        }

        return redirect()->route('index');
    }

    /**
     * Should display installer page
     */
    public function shouldDisplay(): bool
    {
        return !app('installer')->hasLicenseCode() || User::administrator()->doesntExist();
    }
}
