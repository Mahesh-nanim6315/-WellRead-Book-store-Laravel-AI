<?php

namespace Tests\Feature\Admin;

use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_with_manage_roles_permissions_can_access_roles_permissions_page(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        RolePermission::create([
            'role' => 'staff',
            'permissions' => ['manage_roles_permissions'],
        ]);

        $this->actingAs($staff)
            ->get('/admin/roles-permissions')
            ->assertOk();
    }

    public function test_staff_without_manage_roles_permissions_cannot_access_roles_permissions_page(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        RolePermission::create([
            'role' => 'staff',
            'permissions' => [],
        ]);

        $this->actingAs($staff)
            ->get('/admin/roles-permissions')
            ->assertForbidden();
    }

    public function test_manager_books_access_follows_saved_permissions(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        RolePermission::create([
            'role' => 'manager',
            'permissions' => [],
        ]);

        $this->actingAs($manager)
            ->get('/admin/books')
            ->assertForbidden();

        RolePermission::where('role', 'manager')->update([
            'permissions' => ['books.view'],
        ]);

        $this->actingAs($manager)
            ->get('/admin/books')
            ->assertOk();
    }

    public function test_dashboard_access_requires_access_dashboard_permission(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertForbidden();

        RolePermission::create([
            'role' => 'user',
            'permissions' => ['access_dashboard'],
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_roles_permissions_update_uses_normalized_lowercase_role_keys(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'Manager']);

        $response = $this->actingAs($admin)->put('/admin/roles-permissions', [
            'permissions' => [
                'manager' => ['manage_orders'],
            ],
        ]);

        $response->assertRedirect(route('admin.roles_permissions.index'));

        $this->assertDatabaseHas('role_permissions', [
            'role' => 'manager',
        ]);
        $this->assertDatabaseMissing('role_permissions', [
            'role' => 'Manager',
        ]);
    }
}
