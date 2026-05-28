<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffController extends Controller implements HasMiddleware
{
    use ScopedToCompany;

    public static function middleware(): array
    {
        return [
            new Middleware('can:payroll.view',   only: ['index', 'show']),
            new Middleware('can:payroll.create', only: ['create', 'store']),
            new Middleware('can:payroll.edit',   only: ['edit', 'update']),
            new Middleware('can:payroll.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = Staff::where('company_id', $this->companyId());

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $staffList = $query->withCount('payrollEntries')->latest()->paginate(15)->withQueryString();

        return view('payroll.staff.index', compact('staffList'));
    }

    public function create(): View
    {
        return view('payroll.staff.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'mobile'      => ['nullable', 'string', 'max:20'],
            'designation' => ['nullable', 'string', 'max:255'],
            'hourly_rate' => ['required', 'numeric', 'min:0'],
            'notes'       => ['nullable', 'string'],
        ]);

        $staff = Staff::create(array_merge($data, [
            'company_id' => $this->companyId(),
            'status'     => 'active',
        ]));

        return redirect()->route('payroll.staff.show', $staff)->with('success', 'Staff member added successfully.');
    }

    public function show(Staff $staff): View
    {
        $this->authorizeCompany($staff);

        $entries = $staff->payrollEntries()
            ->latest('entry_date')
            ->paginate(20);

        return view('payroll.staff.show', compact('staff', 'entries'));
    }

    public function edit(Staff $staff): View
    {
        $this->authorizeCompany($staff);
        return view('payroll.staff.form', compact('staff'));
    }

    public function update(Request $request, Staff $staff): RedirectResponse
    {
        $this->authorizeCompany($staff);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'mobile'      => ['nullable', 'string', 'max:20'],
            'designation' => ['nullable', 'string', 'max:255'],
            'hourly_rate' => ['required', 'numeric', 'min:0'],
            'status'      => ['required', 'in:active,inactive'],
            'notes'       => ['nullable', 'string'],
        ]);

        $staff->update($data);

        return redirect()->route('payroll.staff.show', $staff)->with('success', 'Staff member updated.');
    }

    public function destroy(Staff $staff): RedirectResponse
    {
        $this->authorizeCompany($staff);
        $staff->delete();
        return redirect()->route('payroll.staff.index')->with('success', 'Staff member deleted.');
    }

    private function authorizeCompany(Staff $staff): void
    {
        abort_if($staff->company_id !== $this->companyId(), 403);
    }
}
