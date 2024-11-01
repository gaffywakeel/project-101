<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class RestrictDemo
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        Gate::denyIf(function (User $user) {
            return config('app.demo') && !$user->is_administrator;
        }, trans('auth.restrict_demo'));

        return $next($request);
    }
}
