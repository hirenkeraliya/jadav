<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\PayrollEntry;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollEntryController extends Controller
{
    use ScopedToCompany;

    public function index(Request $request): View
    {
        $query = PayrollEntry::where('payroll_entries.company_id', $this->companyId())
            ->with('staff');

        if ($staffId = $request->input('staff_id')) {
            $query->where('staff_id', $staffId);
        }

        if ($date = $request->input('date')) {
            $query->whereDate('entry_date', $date);
        }

        $entries   = $query->latest('entry_date')->latest('start_time')->paginate(20)->withQueryString();
        $staffList = Staff::where('company_id', $this->companyId())->where('status', 'active')->orderBy('name')->get();

        return view('payroll.entries.index', compact('entries', 'staffList'));
    }

    public function create(Request $request): View
    {
        $staffList     = Staff::where('company_id', $this->companyId())->where('status', 'active')->orderBy('name')->get();
        $selectedStaff = $request->input('staff_id');
        return view('payroll.entries.form', compact('staffList', 'selectedStaff'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'staff_id'   => ['required', 'integer', 'exists:staff,id'],
            'entry_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'notes'      => ['nullable', 'string'],
        ]);

        // Verify staff belongs to this company
        $staff = Staff::where('id', $data['staff_id'])->where('company_id', $this->companyId())->firstOrFail();

        $hours = $this->calculateHours($data['start_time'], $data['end_time']);

        PayrollEntry::create(array_merge($data, [
            'company_id' => $this->companyId(),
            'hours'      => $hours,
        ]));

        return redirect()->route('payroll.entries.index', ['staff_id' => $staff->id])
            ->with('success', 'Time entry added.');
    }

    public function edit(PayrollEntry $payrollEntry): View
    {
        $this->authorizeCompany($payrollEntry);
        $staffList = Staff::where('company_id', $this->companyId())->where('status', 'active')->orderBy('name')->get();
        return view('payroll.entries.form', compact('payrollEntry', 'staffList'));
    }

    public function update(Request $request, PayrollEntry $payrollEntry): RedirectResponse
    {
        $this->authorizeCompany($payrollEntry);

        $data = $request->validate([
            'staff_id'   => ['required', 'integer', 'exists:staff,id'],
            'entry_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'notes'      => ['nullable', 'string'],
        ]);

        // Verify staff belongs to this company
        Staff::where('id', $data['staff_id'])->where('company_id', $this->companyId())->firstOrFail();

        $data['hours'] = $this->calculateHours($data['start_time'], $data['end_time']);

        $payrollEntry->update($data);

        return redirect()->route('payroll.entries.index', ['staff_id' => $payrollEntry->staff_id])
            ->with('success', 'Time entry updated.');
    }

    public function destroy(PayrollEntry $payrollEntry): RedirectResponse
    {
        $this->authorizeCompany($payrollEntry);
        $staffId = $payrollEntry->staff_id;
        $payrollEntry->delete();
        return redirect()->route('payroll.entries.index', ['staff_id' => $staffId])
            ->with('success', 'Time entry deleted.');
    }

    private function calculateHours(string $start, string $end): float
    {
        $startCarbon = Carbon::createFromFormat('H:i', $start);
        $endCarbon   = Carbon::createFromFormat('H:i', $end);
        return round($endCarbon->diffInMinutes($startCarbon) / 60, 2);
    }

    private function authorizeCompany(PayrollEntry $entry): void
    {
        abort_if($entry->company_id !== $this->companyId(), 403);
    }
}
