<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireModuleAccess
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        abort_unless($request->user()?->hasModule($module), 403, 'You do not have access to this module.');

        return $next($request);
    }
}