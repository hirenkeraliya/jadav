<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\FinanceEntry;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    use ScopedToCompany;

    public function index(): View
    {
        return view('reports.index');
    }

    public function finance(Request $request): View
    {
        $cid = $this->companyId();
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $entries = FinanceEntry::where('company_id', $cid)
            ->whereBetween('date', [$from, $to])
            ->with(['project.customer', 'entryType', 'paymentType'])
            ->orderBy('date')
            ->get();

        $totalCredit = $entries->where('type', 'credit')->sum('amount');
        $totalDebit  = $entries->where('type', 'debit')->sum('amount');
        $netBalance  = $totalCredit - $totalDebit;

        $byProject = $entries->groupBy('project_id')->map(function ($group) {
            return [
                'project'  => $group->first()->project,
                'received' => $group->where('type', 'credit')->sum('amount'),
                'expense'  => $group->where('type', 'debit')->sum('amount'),
            ];
        });

        return view('reports.finance', compact('entries', 'totalCredit', 'totalDebit', 'netBalance', 'byProject', 'from', 'to'));
    }

    public function quotations(Request $request): View
    {
        $cid = $this->companyId();
        $from = $request->input('from', now()->startOfYear()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $total     = Quotation::where('company_id', $cid)->whereBetween('date', [$from, $to])->count();
        $converted = Quotation::where('company_id', $cid)->whereBetween('date', [$from, $to])->where('status', 'converted')->count();
        $accepted  = Quotation::where('company_id', $cid)->whereBetween('date', [$from, $to])->where('status', 'accepted')->count();
        $rejected  = Quotation::where('company_id', $cid)->whereBetween('date', [$from, $to])->where('status', 'rejected')->count();
        $conversionRate = $total > 0 ? round(($converted / $total) * 100, 1) : 0;

        $totalValue     = Quotation::where('company_id', $cid)->whereBetween('date', [$from, $to])->sum('total');
        $convertedValue = Quotation::where('company_id', $cid)->whereBetween('date', [$from, $to])->where('status', 'converted')->sum('total');

        $quotations = Quotation::where('company_id', $cid)->whereBetween('date', [$from, $to])->with('customer')->latest()->get();

        return view('reports.quotations', compact(
            'total', 'converted', 'accepted', 'rejected', 'conversionRate',
            'totalValue', 'convertedValue', 'quotations', 'from', 'to'
        ));
    }

    public function projects(Request $request): View
    {
        $cid = $this->companyId();

        $projectsByStatus = Project::where('company_id', $cid)
            ->selectRaw('status, count(*) as total, sum(estimated_amount) as total_estimated')
            ->groupBy('status')
            ->get();

        $invoiceSummary = Invoice::where('company_id', $cid)
            ->selectRaw('sum(total) as invoiced, sum(paid_amount) as collected')
            ->first();

        $projects = Project::where('company_id', $cid)
            ->with(['customer', 'projectType'])
            ->withSum(['financeEntries as total_received' => fn($q) => $q->where('type', 'credit')], 'amount')
            ->withSum(['financeEntries as total_expense' => fn($q) => $q->where('type', 'debit')], 'amount')
            ->latest()
            ->get();

        return view('reports.projects', compact('projectsByStatus', 'invoiceSummary', 'projects'));
    }
}
