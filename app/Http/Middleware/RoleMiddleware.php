<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

     /** @var \App\Models\User */
    
     public function handle($request, Closure $next, $role)
     {
        /** @var \App\Models\User */
         $user = Auth::user();
         Log::info('User Role Check', ['user' => $user, 'role' => $role, 'hasRole' => $user->hasRole($role)]);
 
         if (Auth::check() && $user->hasRole($role)) {
             return $next($request);
         }
 
         return redirect('/');
     }
}
