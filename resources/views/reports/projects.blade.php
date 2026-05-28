@extends('layouts.app')
@section('title', 'Projects Report')
@section('breadcrumb')
  <a href="{{ route('reports.index') }}" style="color:#8b5cf6;text-decoration:none">Reports</a> / Projects
@endsection

@section('content')
<h1 class="page-title" style="margin-bottom:24px">Projects Report</h1>

{{-- Status breakdown --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:14px;margin-bottom:24px">
  @foreach($statusBreakdown as $status => $count)
  <div class="stat-card">
    <div class="stat-value">{{ $count }}</div>
    <div style="margin-top:6px"><span class="badge badge-{{ $status }}">{{ ucfirst(str_replace('_',' ',$status)) }}</span></div>
  </div>
  @endforeach
</div>

<div class="card">
  <div class="card-header"><span style="font-weight:700">All Projects</span></div>
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Project</th>
          <thClient</th>
          <th>Status</th>
          <th>Priority</th>
          <th>Start Date</th>
          <th style="text-align:right">Tasks</th>
        </tr>
      </thead>
      <tbody>
        @forelse($projects as $project)
        <tr>
          <td>
            <a href="{{ route('projects.show', $project) }}" style="font-weight:700;color:#6366f1;text-decoration:none">{{ $project->name }}</a>
            @foreach($project->projectTypes as $pt) <span style="font-size:0.72rem;color:#9ca3af">{{ $pt->name }}</span> @endforeach
          </td>
          <td>{{ $project->customer->name ?? '—' }}</td>
          <td><span class="badge badge-{{ $project->status }}">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span></td>
          <td><span class="badge badge-{{ $project->priority }}">{{ ucfirst($project->priority) }}</span></td>
          <td style="color:#9ca3af;font-size:0.82rem">{{ $project->start_date ? $project->start_date->format('d M Y') : '—' }}</td>
          <td style="text-align:right;color:#9ca3af">{{ $project->tasks_count ?? 0 }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:24px">No projects.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
