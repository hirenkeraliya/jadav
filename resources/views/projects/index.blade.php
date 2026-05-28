@extends('layouts.app')
@section('title', 'Projects')
@section('breadcrumb', 'Projects')

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Projects</h1>
    <p class="page-subtitle">{{ $projects->total() }} total projects</p>
  </div>
  <a href="{{ route('projects.create') }}" class="btn btn-primary">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Project
  </a>
</div>

{{-- Status tabs --}}
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px">
  @php $statuses = ['','pending','running','on_hold','delayed','completed','invoiced','cancelled']; @endphp
  @foreach($statuses as $s)
  <a href="{{ route('projects.index', array_merge(request()->query(), ['status' => $s])) }}"
     style="padding:6px 14px;border-radius:20px;font-size:0.8rem;font-weight:600;text-decoration:none;transition:all 0.2s;
            {{ (request('status') === $s || (!request('status') && $s === '')) ? 'background:#6366f1;color:#fff' : 'background:#fff;color:#6b7280;border:1px solid #e5e7eb' }}">
    {{ $s === '' ? 'All' : ucfirst(str_replace('_', ' ', $s)) }}
  </a>
  @endforeach
</div>

{{-- Search --}}
<div class="card mb-5">
  <div class="card-body" style="padding:14px 20px">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
      @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
      <input type="text" name="search" class="form-control" style="max-width:260px"
             placeholder="Search project name, code..." value="{{ request('search') }}">
      <select name="priority" class="form-control" style="max-width:140px">
        <option value="">All priorities</option>
        <option value="high" {{ request('priority')=='high'?'selected':'' }}>High</option>
        <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Medium</option>
        <option value="low" {{ request('priority')=='low'?'selected':'' }}>Low</option>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      @if(request()->except('status') != [])
        <a href="{{ route('projects.index', request('status') ? ['status' => request('status')] : []) }}" class="btn btn-secondary btn-sm">Clear</a>
      @endif
    </form>
  </div>
</div>

<div class="card">
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Project</th>
          <th>Customer</th>
          <th>Type</th>
          <th>Status</th>
          <th>Priority</th>
          <th>Timeline</th>
          <th style="text-align:right">Received</th>
          <th style="text-align:right">Expense</th>
          <th style="text-align:right">P&amp;L</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($projects as $project)
        <tr>
          <td>
            <span style="display:inline-block;background:#ede9fe;color:#6d28d9;font-size:0.95rem;font-weight:800;letter-spacing:0.05em;padding:3px 10px;border-radius:6px;margin-bottom:5px">{{ $project->project_code }}</span>
            <div><a href="{{ route('projects.show', $project) }}" style="font-weight:600;color:#4f46e5;text-decoration:none">{{ $project->name }}</a></div>
          </td>
          <td>{{ $project->customer->name ?? '—' }}</td>
          <td>
            @foreach($project->projectTypes as $pt)
              <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.78rem;white-space:nowrap;background:#f3f4f6;padding:2px 7px;border-radius:20px;margin-bottom:2px">
                <span style="width:7px;height:7px;border-radius:50%;background:{{ $pt->color }}"></span>
                {{ $pt->name }}
              </span><br>
            @endforeach
            @if($project->projectTypes->isEmpty())
              <span style="color:#9ca3af">—</span>
            @endif
          </td>
          <td><span class="badge badge-{{ $project->status }}">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span></td>
          <td><span class="badge badge-{{ $project->priority }}">{{ ucfirst($project->priority) }}</span></td>
          <td style="min-width:160px">
            @php
              $isActive   = !in_array($project->status, ['completed', 'cancelled', 'invoiced']);
              $today      = now()->startOfDay();
              $start      = $project->start_date;
              $end        = $project->end_date;
              $totalDays  = ($start && $end) ? (int) $start->diffInDays($end) : null;
              $spentDays  = $start ? (int) $start->diffInDays(min($today, $end ?? $today)) : null;
              $remaining  = $end ? (int) $today->diffInDays($end, false) : null; // negative = overdue
            @endphp

            @if($start || $end)
              {{-- Dates row --}}
              <div style="font-size:0.78rem;color:#6b7280">
                {{ $start ? $start->format('d M Y') : '—' }}
                @if($end) → {{ $end->format('d M Y') }} @endif
              </div>

              @if($totalDays !== null)
              {{-- Duration --}}
              <div style="font-size:0.75rem;color:#9ca3af;margin-top:1px">
                {{ $totalDays }} day{{ $totalDays !== 1 ? 's' : '' }} total
              </div>
              @endif

              @if($end && $isActive)
                @if($remaining < 0)
                  {{-- Overdue --}}
                  <div style="font-size:0.75rem;font-weight:700;color:#ef4444;margin-top:3px">
                    {{ abs($remaining) }} day{{ abs($remaining) !== 1 ? 's' : '' }} overdue
                  </div>
                @elseif($remaining === 0)
                  <div style="font-size:0.75rem;font-weight:700;color:#f59e0b;margin-top:3px">Due today</div>
                @else
                  <div style="font-size:0.75rem;color:#10b981;font-weight:600;margin-top:3px">
                    {{ $remaining }} day{{ $remaining !== 1 ? 's' : '' }} left
                  </div>
                @endif
              @elseif($end && !$isActive)
                <div style="font-size:0.75rem;color:#9ca3af;margin-top:3px">Closed</div>
              @endif
            @else
              <span style="color:#9ca3af">—</span>
            @endif
          </td>
          @php
            $received = (float) ($project->total_received ?? 0);
            $expense  = (float) ($project->total_expense ?? 0);
            $pl       = $received - $expense;
          @endphp
          <td style="text-align:right;font-size:0.85rem;color:#10b981;font-weight:600;white-space:nowrap">
            {{ $activeCompany->currency_symbol }}{{ number_format($received, 0) }}
          </td>
          <td style="text-align:right;font-size:0.85rem;color:#ef4444;font-weight:600;white-space:nowrap">
            {{ $activeCompany->currency_symbol }}{{ number_format($expense, 0) }}
          </td>
          <td style="text-align:right;font-size:0.85rem;font-weight:700;white-space:nowrap;{{ $pl >= 0 ? 'color:#10b981' : 'color:#ef4444' }}">
            {{ $pl >= 0 ? '+' : '' }}{{ $activeCompany->currency_symbol }}{{ number_format($pl, 0) }}
          </td>
          <td>
            <div style="display:flex;gap:6px;justify-content:flex-end">
              <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary btn-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> View
              </a>
              <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary btn-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
              </a>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="10" style="text-align:center;padding:48px;color:#9ca3af">
            No projects found. <a href="{{ route('projects.create') }}" style="color:#6366f1;font-weight:600">Create your first project →</a>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($projects->hasPages())
  <div style="padding:16px 20px;border-top:1px solid #f3f4f6">{{ $projects->links() }}</div>
  @endif
</div>
@endsection
