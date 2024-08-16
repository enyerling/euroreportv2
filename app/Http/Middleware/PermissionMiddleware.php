<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        if (Auth::check() && Auth::users()->hasPermission($permission)) {
            return $next($request);
        }

        return redirect('/home')->with('error', 'No tienes permiso para realizar esta acción.');
    }
}
