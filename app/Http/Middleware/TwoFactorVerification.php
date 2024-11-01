<?php

namespace App\Http\Middleware;

use App\Rules\TwoFactorToken;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (\Illuminate\Http\Response|RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->isTwoFactorEnabled()) {
            $request->validate([
                'token' => [
                    'required', 'string', 'max:100',
                    new TwoFactorToken,
                ],
            ]);
        } else {
            $request->validate([
                'password' => [
                    'required', 'string', 'max:100',
                    'current_password',
                ],
            ]);
        }

        return $next($request);
    }
}
