<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictModule
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $name): Response
    {
        if (!$this->getModule($name)?->isEnabled()) {
            abort(403, trans('auth.action_forbidden'));
        }

        return $next($request);
    }

    /**
     * Get module object
     */
    protected function getModule($name): ?Module
    {
        return Module::find($name);
    }
}
