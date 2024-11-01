<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RequireInstaller
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): \Symfony\Component\HttpFoundation\Response
    {
        if (User::administrator()->doesntExist()) {
            return Response::redirectTo('installer');
        }

        return $next($request);
    }
}
