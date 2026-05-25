<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImpersonateController extends Controller
{
    public function impersonate(Request $request, User $user): RedirectResponse
    {
        $admin = $request->user();

        if (! $admin->is_super_admin) {
            abort(403);
        }

        session(['impersonating_admin_id' => $admin->id]);
        auth()->loginUsingId($user->id);

        $target = auth()->user();
        $target->update(['impersonating_id' => $admin->id]);

        if (! $target->active_company_id) {
            $first = $target->companies()->first();
            if ($first) $target->update(['active_company_id' => $first->id]);
        }

        return redirect()->route('dashboard')->with('info', 'You are now impersonating ' . $user->name);
    }

    public function leave(Request $request): RedirectResponse
    {
        $adminId = session('impersonating_admin_id');

        if (! $adminId) {
            return redirect()->route('dashboard');
        }

        $current = $request->user();
        $current->update(['impersonating_id' => null]);

        session()->forget('impersonating_admin_id');
        auth()->loginUsingId($adminId);

        return redirect()->route('dashboard')->with('success', 'Returned to your account.');
    }
}
