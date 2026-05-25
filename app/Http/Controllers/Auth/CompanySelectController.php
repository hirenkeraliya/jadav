<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanySelectController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $companies = $user->companies()->where('is_active', true)->get();

        if ($companies->isEmpty()) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account is not assigned to any company.']);
        }

        return view('auth.company-select', compact('companies'));
    }

    public function select(Request $request): RedirectResponse
    {
        $request->validate(['company_id' => ['required', 'integer']]);

        $user = $request->user();
        $company = $user->companies()->findOrFail($request->company_id);

        $user->update(['active_company_id' => $company->id]);

        return redirect()->intended(route('dashboard'));
    }

    public function switch(Request $request): RedirectResponse
    {
        $request->validate(['company_id' => ['required', 'integer']]);

        $user = $request->user();
        $company = $user->is_super_admin
            ? \App\Models\Company::findOrFail($request->company_id)
            : $user->companies()->findOrFail($request->company_id);

        $user->update(['active_company_id' => $company->id]);

        return redirect()->back()->with('success', 'Switched to ' . $company->name);
    }
}
