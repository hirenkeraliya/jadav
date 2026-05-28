<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Permissions grouped by module.
     * Format: 'module.action'
     */
    public static array $permissions = [
        'customers'  => ['view', 'create', 'edit', 'delete'],
        'projects'   => ['view', 'create', 'edit', 'delete', 'change_status'],
        'tasks'      => ['view', 'create', 'edit', 'delete'],
        'finance'    => ['view', 'create', 'edit', 'delete'],
        'variations' => ['view', 'create', 'edit', 'delete'],
        'quotations' => ['view', 'create', 'edit', 'delete'],
        'reports'    => ['view'],
        'payroll'    => ['view', 'create', 'edit', 'delete'],
        'settings'   => ['view', 'edit'],
        'users'      => ['view', 'create', 'edit', 'delete'],
        'roles'      => ['view', 'create', 'edit', 'delete'],
    ];

    public function run(): void
    {
        // Create all permissions
        foreach (self::$permissions as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    ['name' => "{$module}.{$action}", 'guard_name' => 'web']
                );
            }
        }

        // Assign all permissions to Admin role
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // Manager: everything except roles/users management and delete settings
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->syncPermissions(
            Permission::whereNotIn('name', [
                'roles.create', 'roles.edit', 'roles.delete',
                'users.create', 'users.edit', 'users.delete',
                'settings.edit',
            ])->get()
        );

        // Staff: view + create + edit on work modules, no finance deletes, no settings/users/roles
        $staff = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $staff->syncPermissions(
            Permission::whereIn('name', [
                'customers.view', 'customers.create', 'customers.edit',
                'projects.view', 'projects.create', 'projects.edit', 'projects.change_status',
                'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
                'finance.view', 'finance.create',
                'variations.view', 'variations.create',
                'quotations.view', 'quotations.create', 'quotations.edit',
                'reports.view',
                'payroll.view',
            ])->get()
        );

        // Viewer: read-only across all modules
        $viewer = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions(
            Permission::where('name', 'like', '%.view')->get()
        );
    }
}
