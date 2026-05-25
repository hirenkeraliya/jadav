<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use ScopedToCompany;

    public function index(): View
    {
        $company = $this->company();
        $users = $company->users()->with('roles')->get();
        return view('settings.users.index', compact('users', 'company'));
    }

    public function adminIndex(): View
    {
        $users = User::with(['roles', 'companies'])->orderBy('name')->get();
        $stats = [
            'total'       => $users->count(),
            'active'      => $users->where('is_active', true)->count(),
            'inactive'    => $users->where('is_active', false)->count(),
            'super_admin' => $users->where('is_super_admin', true)->count(),
        ];
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'stats', 'roles'));
    }

    public function adminCreate(): View
    {
        $companies = Company::where('is_active', true)->get();
        $roles = Role::all();
        return view('admin.users.form', compact('companies', 'roles'));
    }

    public function adminStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'password'     => ['required', Password::defaults(), 'confirmed'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'role'         => ['nullable', 'string', 'exists:roles,name'],
            'companies'    => ['nullable', 'array'],
            'companies.*'  => ['exists:companies,id'],
            'is_active'    => ['nullable'],
            'is_super_admin' => ['nullable'],
        ]);

        $user = User::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => Hash::make($data['password']),
            'phone'          => $data['phone'] ?? null,
            'is_active'      => isset($data['is_active']),
            'is_super_admin' => isset($data['is_super_admin']),
        ]);

        if (!empty($data['companies'])) {
            $user->companies()->attach($data['companies']);
        }
        if (!empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function adminEdit(User $user): View
    {
        $companies = Company::where('is_active', true)->get();
        $roles = Role::all();
        return view('admin.users.form', compact('user', 'companies', 'roles'));
    }

    public function adminUpdate(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email,' . $user->id],
            'password'     => ['nullable', Password::defaults(), 'confirmed'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'role'         => ['nullable', 'string', 'exists:roles,name'],
            'companies'    => ['nullable', 'array'],
            'companies.*'  => ['exists:companies,id'],
            'is_active'    => ['nullable'],
            'is_super_admin' => ['nullable'],
        ]);

        $user->update([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'phone'          => $data['phone'] ?? null,
            'is_active'      => isset($data['is_active']),
            'is_super_admin' => auth()->user()->is_super_admin ? isset($data['is_super_admin']) : $user->is_super_admin,
        ]);

        if (!is_null($data['password'] ?? null)) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $user->companies()->sync($data['companies'] ?? []);
        $user->syncRoles($data['role'] ? [$data['role']] : []);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function adminDestroy(User $user): RedirectResponse
    {
        if ($user->is_super_admin) {
            abort(403, 'Cannot delete a super admin.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    public function create(): View
    {
        $companies = Company::where('is_active', true)->get();
        $roles = Role::all();
        return view('settings.users.create', compact('companies', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', Password::defaults(), 'confirmed'],
            'phone'      => ['nullable', 'string'],
            'companies'  => ['required', 'array'],
            'companies.*'=> ['exists:companies,id'],
            'role'       => ['required', 'string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name'  => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
        ]);

        $user->companies()->attach($data['companies']);
        $user->assignRole($data['role']);

        return redirect()->route('settings.users')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $companies = Company::where('is_active', true)->get();
        $roles = Role::all();
        $userCompanies = $user->companies->pluck('id')->toArray();
        return view('settings.users.edit', compact('user', 'companies', 'roles', 'userCompanies'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone'      => ['nullable', 'string'],
            'companies'  => ['required', 'array'],
            'companies.*'=> ['exists:companies,id'],
            'role'       => ['required', 'string', 'exists:roles,name'],
        ]);

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        $user->companies()->sync($data['companies']);
        $user->syncRoles([$data['role']]);

        return redirect()->route('settings.users')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is_super_admin) abort(403, 'Cannot delete super admin.');
        $user->delete();
        return redirect()->route('settings.users')->with('success', 'User deleted.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password reset successfully.');
    }
}
