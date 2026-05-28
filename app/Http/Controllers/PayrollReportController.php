<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\PayrollEntry;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollReportController extends Controller
{
    use ScopedToCompany;

    public function index(Request $request): View
    {
        $month   = (int) ($request->input('month', now()->month));
        $year    = (int) ($request->input('year', now()->year));
        $staffId = $request->input('staff_id');

        $staffList = Staff::where('company_id', $this->companyId())
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        $query = PayrollEntry::where('payroll_entries.company_id', $this->companyId())
            ->whereBetween('entry_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with('staff');

        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        $entries = $query->orderBy('staff_id')->orderBy('entry_date')->orderBy('start_time')->get();

        // Group entries by staff and compute totals
        $report = [];
        foreach ($entries as $entry) {
            $sid = $entry->staff_id;
            if (!isset($report[$sid])) {
                $report[$sid] = [
                    'staff'        => $entry->staff,
                    'total_hours'  => 0,
                    'entries'      => [],
                ];
            }
            $report[$sid]['total_hours'] += (float) $entry->hours;
            $report[$sid]['entries'][]    = $entry;
        }

        // Compute payable amounts
        foreach ($report as &$row) {
            $row['payable_amount'] = round($row['total_hours'] * (float) $row['staff']->hourly_rate, 2);
        }
        unset($row);

        $grandTotalHours   = array_sum(array_column($report, 'total_hours'));
        $grandTotalPayable = array_sum(array_column($report, 'payable_amount'));

        $months = $this->monthsList();

        return view('payroll.report.index', compact(
            'report',
            'staffList',
            'month',
            'year',
            'staffId',
            'grandTotalHours',
            'grandTotalPayable',
            'months',
            'startDate',
            'endDate'
        ));
    }

    private function monthsList(): array
    {
        return [
            1  => 'January',   2  => 'February',  3  => 'March',
            4  => 'April',     5  => 'May',        6  => 'June',
            7  => 'July',      8  => 'August',     9  => 'September',
            10 => 'October',   11 => 'November',   12 => 'December',
        ];
    }
}
