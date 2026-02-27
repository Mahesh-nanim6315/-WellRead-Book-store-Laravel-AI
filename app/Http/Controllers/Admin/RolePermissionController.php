<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    private array $defaultRoles = ['admin', 'manager', 'staff', 'user'];

    private array $permissionLabels = [
        'access_dashboard' => 'Access Dashboard',
        'manage_orders' => 'Manage Orders',
        'manage_payments' => 'Manage Payments',
        'books.view' => 'Books: View',
        'books.create' => 'Books: Create',
        'books.edit' => 'Books: Edit/Update',
        'books.delete' => 'Books: Delete',
        'authors.view' => 'Authors: View',
        'authors.create' => 'Authors: Create',
        'authors.edit' => 'Authors: Edit/Update',
        'authors.delete' => 'Authors: Delete',
        'users.view' => 'Users: View',
        'users.create' => 'Users: Create',
        'users.edit' => 'Users: Edit/Update',
        'users.delete' => 'Users: Delete',
        'manage_reviews' => 'Manage Reviews',
        'manage_notifications' => 'Manage Notifications',
        'manage_roles_permissions' => 'Manage Roles & Permissions',
    ];

    public function index()
    {
        $roles = $this->roles();
        $savedPermissions = [];
        foreach (RolePermission::query()->pluck('permissions', 'role')->toArray() as $role => $permissions) {
            $savedPermissions[strtolower((string) $role)] = is_array($permissions) ? $permissions : [];
        }
        $defaultPermissions = $this->defaultPermissions();

        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[$role] = $savedPermissions[$role] ?? ($defaultPermissions[$role] ?? []);
        }

        return view('admin.roles_permissions.index', [
            'roles' => $roles,
            'permissionLabels' => $this->permissionLabels,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    public function update(Request $request)
    {
        $allowedPermissions = array_keys($this->permissionLabels);
        $roles = $this->roles();
        $permissionsInput = $request->input('permissions', []);

        foreach ($roles as $role) {
            $selectedPermissions = $permissionsInput[$role] ?? [];

            if (!is_array($selectedPermissions)) {
                $selectedPermissions = [];
            }

            $filteredPermissions = array_values(array_unique(array_intersect(
                $allowedPermissions,
                $selectedPermissions
            )));

            // Keep admin immutable: full access remains guaranteed by middleware.
            if ($role === 'admin') {
                continue;
            }

            $rolePermission = RolePermission::query()
                ->whereRaw('LOWER(role) = ?', [$role])
                ->first();

            if ($rolePermission) {
                $rolePermission->update([
                    'role' => $role,
                    'permissions' => $filteredPermissions,
                ]);
            } else {
                RolePermission::create([
                    'role' => $role,
                    'permissions' => $filteredPermissions,
                ]);
            }
        }

        return redirect()
            ->route('admin.roles_permissions.index')
            ->with('success', 'Roles and permissions updated successfully.');
    }

    private function roles(): array
    {
        $dbRoles = User::query()
            ->whereNotNull('role')
            ->pluck('role')
            ->filter()
            ->map(fn ($role) => strtolower((string) $role))
            ->unique()
            ->values()
            ->all();

        return array_values(array_unique(array_merge($this->defaultRoles, $dbRoles)));
    }

    private function defaultPermissions(): array
    {
        return [
            'admin' => array_keys($this->permissionLabels),
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
