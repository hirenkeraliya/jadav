<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        $companies = Company::withCount('users')->latest()->get();
        return view('admin.companies.index', compact('companies'));
    }

    public function create(): View
    {
        return view('admin.companies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCompany($request);
        $data['logo'] = $this->handleLogo($request);

        Company::create($data);

        return redirect()->route('admin.companies.index')->with('success', 'Company created.');
    }

    public function edit(Company $company): View
    {
        $users = User::all();
        return view('admin.companies.edit', compact('company', 'users'));
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $data = $this->validateCompany($request, $company->id);
        if ($request->hasFile('logo')) {
            if ($company->logo) Storage::disk('public')->delete($company->logo);
            $data['logo'] = $this->handleLogo($request);
        }

        $company->update($data);

        return redirect()->route('admin.companies.edit', $company)->with('success', 'Company updated.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        if ($company->logo) Storage::disk('public')->delete($company->logo);
        $company->delete();
        return redirect()->route('admin.companies.index')->with('success', 'Company deleted.');
    }

    public function show(Company $company): View
    {
        return view('admin.companies.show', compact('company'));
    }

    private function validateCompany(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email'],
            'phone'                 => ['nullable', 'string'],
            'address'               => ['nullable', 'string'],
            'logo'                  => ['nullable', 'image', 'max:2048'],
            'primary_color'         => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'secondary_color'       => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'currency'              => ['required', 'string', 'max:10'],
            'currency_symbol'       => ['required', 'string', 'max:5'],
            'tax_label'             => ['nullable', 'string'],
            'tax_number'            => ['nullable', 'string'],
            'website'               => ['nullable', 'url'],
            'invoice_prefix'        => ['required', 'string', 'max:20'],
            'quotation_prefix'      => ['required', 'string', 'max:20'],
            'financial_year_start'  => ['required', 'integer', 'between:1,12'],
        ]);
    }

    private function handleLogo(Request $request): ?string
    {
        if ($request->hasFile('logo')) {
            return $request->file('logo')->store('logos', 'public');
        }
        return null;
    }
}
