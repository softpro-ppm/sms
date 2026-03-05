<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Admin has access to everything
        if ($user->role === 'admin') {
            return $next($request);
        }

        $roles = array_filter(array_map('trim', array_merge(...array_map(function ($role) {
            return explode(',', (string) $role);
        }, $roles))));

        // Check specific role(s)
        if (!in_array($user->role, $roles, true)) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
