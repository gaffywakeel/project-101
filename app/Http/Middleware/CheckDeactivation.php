<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDeactivation
{
    /**
     * Handle an incoming request.
     *
     *
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->isDeactivated()) {
            $date = $request->user()->deactivated_until;

            $message = trans('auth.deactivated', [
                'date' => $date->toFormattedDateString(),
            ]);

            throw new AuthenticationException($message);
        }

        return $next($request);
    }
}
