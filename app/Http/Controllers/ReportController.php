<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\FinanceEntry;
use App\Models\Project;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ReportController extends Controller implements HasMiddleware
{
    use ScopedToCompany;

    public static function middleware(): array
    {
        return [new Middleware('can:reports.view')];
    }

    public function index(): View
    {
        return view('reports.index');
    }

    public function finance(Request $request): View
    {
        $cid     = $this->companyId();
        $company = $this->company();
        $from    = $request->input('from', now()->startOfMonth()->toDateString());
        $to      = $request->input('to', now()->toDateString());
        $groupBy = $request->input('group_by', 'project');

        $entries = FinanceEntry::where('company_id', $cid)
            ->whereBetween('date', [$from, $to])
            ->with(['project.customer', 'entryType', 'paymentType'])
            ->orderBy('date')
            ->get();

        $summary = [
            'total_credit' => $entries->where('type', 'credit')->sum('amount'),
            'total_debit'  => $entries->where('type', 'debit')->sum('amount'),
            'net'          => $entries->where('type', 'credit')->sum('amount') - $entries->where('type', 'debit')->sum('amount'),
        ];

        $grouped = $entries->groupBy(function ($e) use ($groupBy) {
            if ($groupBy === 'month') return $e->date->format('Y-m');
            if ($groupBy === 'type')  return $e->finance_entry_type_id ?? 'none';
            return $e->project_id ?? 'none';
        })->map(function ($group) use ($groupBy) {
            $first = $group->first();
            if ($groupBy === 'month') {
                $label = $first->date->format('M Y');
            } elseif ($groupBy === 'type') {
                $label = $first->entryType->name ?? 'No Type';
            } else {
                $label = $first->project->name ?? 'No Project';
            }
            return (object) [
                'group_label'  => $label,
                'total_credit' => $group->where('type', 'credit')->sum('amount'),
                'total_debit'  => $group->where('type', 'debit')->sum('amount'),
            ];
        })->values();

        return view('reports.finance', compact('entries', 'summary', 'grouped', 'company', 'from', 'to', 'groupBy'));
    }

    public function quotations(Request $request): View
    {
        $cid     = $this->companyId();
        $company = $this->company();
        $from    = $request->input('from', now()->startOfYear()->toDateString());
        $to      = $request->input('to', now()->toDateString());

        $base = Quotation::where('company_id', $cid)->whereBetween('date', [$from, $to]);

        $stats = [
            'total'          => (clone $base)->count(),
            'sent'           => (clone $base)->where('status', 'sent')->count(),
            'accepted'       => (clone $base)->where('status', 'accepted')->count(),
            'rejected'       => (clone $base)->where('status', 'rejected')->count(),
            'converted'      => (clone $base)->where('status', 'converted')->count(),
            'total_value'    => (clone $base)->sum('total'),
            'accepted_value' => (clone $base)->where('status', 'accepted')->sum('total'),
            'pending_value'  => (clone $base)->whereIn('status', ['draft', 'sent'])->sum('total'),
        ];

        $quotations = (clone $base)->with('customer')->latest()->get();

        return view('reports.quotations', compact('stats', 'company', 'quotations', 'from', 'to'));
    }

    public function projects(Request $request): View
    {
        $cid     = $this->companyId();
        $company = $this->company();

        $statusBreakdown = Project::where('company_id', $cid)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $projects = Project::where('company_id', $cid)
            ->with(['customer', 'projectTypes'])
            ->withCount('tasks')
            ->latest()
            ->get();

        return view('reports.projects', compact('statusBreakdown', 'projects', 'company'));
    }
}
