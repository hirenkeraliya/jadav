@extends('layouts.app')
@section('title', 'My Tasks')
@section('breadcrumb', 'Tasks')

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">My Tasks</h1>
    <p class="page-subtitle">Assigned to you across all projects</p>
  </div>
</div>

@php
  $byProject = $tasks->groupBy(fn($t) => $t->project->name ?? 'Unassigned');
@endphp

@forelse($byProject as $projectName => $projectTasks)
<div class="card mb-4">
  <div class="card-header">
    <span style="font-weight:700;color:#1e1b4b">{{ $projectName }}</span>
    <span style="font-size:0.8rem;color:#8b5cf6">{{ $projectTasks->count() }} task(s)</span>
  </div>
  <div>
    @foreach($projectTasks->sortBy('status') as $task)
    <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #f3f4f6">
      <form method="POST" action="{{ route('tasks.update', [$task->project, $task]) }}">
        @csrf @method('PUT')
        <input type="hidden" name="status" value="{{ $task->status === 'completed' ? 'pending' : 'completed' }}">
        <input type="hidden" name="title" value="{{ $task->title }}">
        <input type="hidden" name="priority" value="{{ $task->priority }}">
        <button type="submit" style="background:none;border:none;cursor:pointer;padding:0;width:22px;height:22px;border-radius:50%;border:2px solid {{ $task->status === 'completed' ? '#10b981' : '#d1d5db' }};background:{{ $task->status === 'completed' ? '#10b981' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
          @if($task->status === 'completed')
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          @endif
        </button>
      </form>
      <div style="flex:1">
        <div style="font-size:0.875rem;font-weight:600;{{ $task->status === 'completed' ? 'text-decoration:line-through;color:#9ca3af' : 'color:#1e1b4b' }}">
          {{ $task->title }}
        </div>
        @if($task->description)
          <div style="font-size:0.78rem;color:#9ca3af;margin-top:2px">{{ Str::limit($task->description, 80) }}</div>
        @endif
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <span class="badge badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
        @if($task->due_date)
          <span style="font-size:0.78rem;font-weight:600;color:{{ $task->due_date->isPast() && $task->status !== 'completed' ? '#ef4444' : '#9ca3af' }}">
            {{ $task->due_date->format('d M') }}
          </span>
        @endif
      </div>
    </div>
    @endforeach
  </div>
</div>
@empty
<div class="card">
  <div class="card-body" style="text-align:center;padding:48px;color:#9ca3af">
    No tasks assigned to you. You're all caught up! 🎉
  </div>
</div>
@endforelse
@endsection
