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
