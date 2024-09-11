<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RoleMiddleware
{
    // Define roles predefinidos
    protected $roles = [
        'admin' => 'admin',
        'subadmin' => 'subamin',
        'user' => 'user',
    ];

    public function handle($request, Closure $next, $role)
    {
        $user = Auth::user();

        // Log para depuración
        Log::info('Checking user role', [
            'user_id' => $user->id,
            'roles' => $user->roles->pluck('name'),
            'required_role' => $role,
            'has_role' => $user->hasRole($role),
        ]);

        if ($user && $user->hasRole($role)) {
            return $next($request);
        }

        return redirect()->route('admin.dashboard');
    }   
}
