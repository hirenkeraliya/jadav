@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
    <p class="page-subtitle">Here's what's happening with {{ $activeCompany->name }} today</p>
  </div>
  <a href="{{ route('projects.create') }}" class="btn btn-primary">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Project
  </a>
</div>

{{-- KPI cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:28px">
  <div class="stat-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
      <span style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#8b5cf6">Total Projects</span>
      <div style="width:36px;height:36px;background:#ede9fe;border-radius:10px;display:flex;align-items:center;justify-content:center">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
      </div>
    </div>
    <div style="font-size:2rem;font-weight:800;color:#1e1b4b">{{ $totalProjects }}</div>
    <div style="font-size:0.78rem;color:#10b981;margin-top:4px">{{ $runningProjects }} active</div>
  </div>

  <div class="stat-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
      <span style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#8b5cf6">Conversion</span>
      <div style="width:36px;height:36px;background:#ede9fe;border-radius:10px;display:flex;align-items:center;justify-content:center">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      </div>
    </div>
    <div style="font-size:2rem;font-weight:800;color:#1e1b4b">{{ $conversionRate }}%</div>
    <div style="font-size:0.78rem;color:#8b5cf6;margin-top:4px">Quotation to project</div>
  </div>
</div>

{{-- Project status breakdown + recent projects --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px">
  {{-- Recent Projects --}}
  <div class="card">
    <div class="card-header">
      <span style="font-weight:700;color:#1e1b4b">Recent Projects</span>
      <a href="{{ route('projects.index') }}" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <div class="table-wrapper">
      <table class="table">
        <thead>
          <tr>
            <th>Project</th>
            <th>Client</th>
            <th>Status</th>
            <th>End Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentProjects as $project)
          <tr>
            <td>
              <a href="{{ route('projects.show', $project) }}" style="font-weight:600;color:#4f46e5;text-decoration:none">
                {{ $project->name }}
              </a>
              <div style="font-size:0.75rem;color:#9ca3af">{{ $project->project_code }}</div>
            </td>
            <td>{{ $project->customer->name ?? '—' }}</td>
            <td><span class="badge badge-{{ $project->status }}">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span></td>
            <td>{{ $project->end_date ? $project->end_date->format('d M Y') : '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:24px">No projects yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Project status doughnut (visual) --}}
  <div class="card">
    <div class="card-header">
      <span style="font-weight:700;color:#1e1b4b">By Status</span>
    </div>
    <div class="card-body">
      @foreach($projectsByStatus as $status => $count)
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
        <div style="display:flex;align-items:center;gap:8px">
          <span class="badge badge-{{ $status }}" style="min-width:80px;justify-content:center">{{ ucfirst(str_replace('_',' ',$status)) }}</span>
        </div>
        <span style="font-weight:700;color:#1e1b4b">{{ $count }}</span>
      </div>
      @endforeach
    </div>
  </div>
</div>

{{-- My Tasks + Upcoming Deadlines --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
  <div class="card">
    <div class="card-header">
      <span style="font-weight:700;color:#1e1b4b">My Pending Tasks</span>
      <a href="{{ route('tasks.mine') }}" class="btn btn-secondary btn-sm">All Tasks</a>
    </div>
    <div class="card-body" style="padding:0">
      @forelse($myTasks as $task)
      <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #f3f4f6">
        <span class="badge badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
        <div style="flex:1;min-width:0">
          <div style="font-weight:600;color:#1e1b4b;font-size:0.875rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $task->title }}</div>
          <div style="font-size:0.75rem;color:#9ca3af">{{ $task->project->name ?? '' }}</div>
        </div>
        @if($task->due_date)
          <span style="font-size:0.75rem;color:{{ $task->due_date->isPast() ? '#ef4444' : '#9ca3af' }};white-space:nowrap">
            {{ $task->due_date->format('d M') }}
          </span>
        @endif
      </div>
      @empty
      <div style="text-align:center;color:#9ca3af;padding:24px;font-size:0.875rem">All caught up! 🎉</div>
      @endforelse
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <span style="font-weight:700;color:#1e1b4b">Upcoming Deadlines</span>
    </div>
    <div class="card-body" style="padding:0">
      @forelse($upcomingDeadlines as $project)
      <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #f3f4f6">
        <div style="width:40px;height:40px;background:#ede9fe;border-radius:10px;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0">
          <span style="font-size:1rem;font-weight:800;color:#6366f1;line-height:1">{{ $project->end_date->format('d') }}</span>
          <span style="font-size:0.6rem;color:#8b5cf6;text-transform:uppercase">{{ $project->end_date->format('M') }}</span>
        </div>
        <div>
          <a href="{{ route('projects.show', $project) }}" style="font-weight:600;color:#1e1b4b;font-size:0.875rem;text-decoration:none">{{ $project->name }}</a>
          <div style="font-size:0.75rem;color:#9ca3af">{{ $project->customer->name ?? '' }}</div>
        </div>
      </div>
      @empty
      <div style="text-align:center;color:#9ca3af;padding:24px;font-size:0.875rem">No upcoming deadlines.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection
