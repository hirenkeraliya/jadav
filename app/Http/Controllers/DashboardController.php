<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopedToCompany;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Task;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use ScopedToCompany;

    public function index(): View
    {
        $cid = $this->companyId();
        $company = $this->company();

        $projectsByStatus = Project::where('company_id', $cid)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalProjects     = Project::where('company_id', $cid)->count();
        $runningProjects   = $projectsByStatus->get('running', 0);
        $completedProjects = $projectsByStatus->get('completed', 0);
        $delayedProjects   = $projectsByStatus->get('delayed', 0);

        $totalQuotations     = Quotation::where('company_id', $cid)->count();
        $convertedQuotations = Quotation::where('company_id', $cid)->where('status', 'converted')->count();
        $conversionRate = $totalQuotations > 0 ? round(($convertedQuotations / $totalQuotations) * 100, 1) : 0;

        $recentProjects = Project::where('company_id', $cid)
            ->with(['customer', 'projectTypes'])
            ->latest()
            ->limit(5)
            ->get();

        $myTasks = Task::whereHas('project', fn($q) => $q->where('company_id', $cid))
            ->where('assigned_to', auth()->id())
            ->whereIn('status', ['pending', 'in_progress'])
            ->with('project')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $upcomingDeadlines = Project::where('company_id', $cid)
            ->whereIn('status', ['running', 'on_hold'])
            ->whereNotNull('end_date')
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->orderBy('end_date')
            ->with('customer')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'company', 'projectsByStatus', 'totalProjects', 'runningProjects',
            'completedProjects', 'delayedProjects', 'totalQuotations',
            'convertedQuotations', 'conversionRate', 'recentProjects',
            'myTasks', 'upcomingDeadlines'
        ));
    }
}
