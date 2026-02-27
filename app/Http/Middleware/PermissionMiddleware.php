<?php

namespace App\Http\Middleware;

use App\Models\RolePermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $role = strtolower($user->role ?? 'user');

        // Admin always has full access, regardless of DB-stored role permissions.
        if ($role === 'admin') {
            return $next($request);
        }

        $permissions = $this->permissionsForRole($role);

        if (!in_array($permission, $permissions, true)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }

    private function permissionsForRole(string $role): array
    {
        $saved = RolePermission::query()
            ->whereRaw('LOWER(role) = ?', [$role])
            ->value('permissions');
        if (is_array($saved)) {
            return $saved;
        }

        return $this->defaultPermissions()[$role] ?? [];
    }

    private function defaultPermissions(): array
    {
        $all = [
            'access_dashboard',
            'manage_orders',
            'manage_payments',
            'books.view',
            'books.create',
            'books.edit',
            'books.delete',
            'authors.view',
            'authors.create',
            'authors.edit',
            'authors.delete',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'manage_reviews',
            'manage_notifications',
            'manage_roles_permissions',
        ];

        return [
            'admin' => $all,
            'manager' => [
                'access_dashboard',
                'manage_orders',
                'books.view',
                'books.create',
                'books.edit',
                'books.delete',
                'authors.view',
                'authors.create',
                'authors.edit',
                'authors.delete',
                'manage_reviews',
                'manage_notifications',
            ],
            'staff' => [
                'access_dashboard',
                'manage_orders',
                'manage_reviews',
            ],
            'user' => [],
        ];
    }
}
