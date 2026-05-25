<?php

namespace App\Http\Controllers;

use Database\Seeders\PermissionSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount(['users', 'permissions'])->orderBy('name')->get();
        $allPermissionsCount = Permission::count();
        return view('admin.roles.index', compact('roles', 'allPermissionsCount'));
    }

    public function create(): View
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });
        return view('admin.roles.form', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$role->name}\" created successfully.");
    }

    public function edit(Role $role): View
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('admin.roles.form', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100', 'unique:roles,name,' . $role->id],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        // Flush the spatie permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$role->name}\" updated successfully.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', "Cannot delete role \"{$role->name}\" — it is assigned to {$role->users()->count()} user(s).");
        }

        $name = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$name}\" deleted.");
    }
}
