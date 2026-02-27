<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
   public function handle($request, Closure $next)
{
    $maintenance = Setting::get('maintenance_mode', 0);

    if ((string) $maintenance === '1') {
        // Always allow logout + password reset flows.
        if ($request->routeIs('logout', 'password.*')) {
            return $next($request);
        }

        // Allow viewing the login page, but prevent non-admin logins.
        // Note: the POST /login route is not named by default, so we check the path.
        if ($request->is('login')) {
            if ($request->isMethod('get')) {
                return $next($request);
            }

            $response = $next($request);

            $user = Auth::user();
            $isAdmin = false;

            if ($user) {
                if (($user->role ?? null) === 'admin') {
                    $isAdmin = true;
                } elseif (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                    $isAdmin = true;
                }
            }

            if ($user && !$isAdmin) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return response()->view('maintenance', status: 503);
            }

            return $response;
        }

        $user = Auth::user();
        $isAdmin = false;

        if ($user) {
            if (($user->role ?? null) === 'admin') {
                $isAdmin = true;
            } elseif (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                $isAdmin = true;
            }
        }

        // Block everyone except admins.
        if (!$isAdmin) {
            if ($user) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return response()->view('maintenance', status: 503);
        }
    }

    return $next($request);
}
}





