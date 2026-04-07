<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChangedIfRequired
{
    /**
     * Staff (admin/reception) with must_change_password must complete change before using the app.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || $user->is_super_admin || !$user->must_change_password) {
            return $next($request);
        }

        // Super Admin “view as centre” — skip force-password for the impersonated staff session
        if ($request->session()->has('impersonation')) {
            return $next($request);
        }

        if ($user->role === 'student' && $user->must_change_password) {
            if ($request->routeIs('student.password.force', 'student.password.force.update', 'logout')) {
                return $next($request);
            }

            return redirect()->route('student.password.force');
        }

        if (!in_array($user->role, ['admin', 'reception'], true)) {
            return $next($request);
        }

        if ($request->routeIs('admin.password.force', 'admin.password.force.update')) {
            return $next($request);
        }

        return redirect()->route('admin.password.force');
    }
}
