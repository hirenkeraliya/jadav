@extends('layouts.app')
@section('title', $project->name)
@section('breadcrumb')
  <a href="{{ route('projects.index') }}" style="color:#8b5cf6;text-decoration:none">Projects</a> /
  {{ $project->name }}
@endsection

@section('content')
<div class="page-header">
  <div>
    <div style="font-size:0.8rem;color:#8b5cf6;font-weight:600;margin-bottom:4px">{{ $project->project_code }}</div>
    <h1 class="page-title">{{ $project->name }}</h1>
    <div style="display:flex;align-items:center;gap:8px;margin-top:6px">
      <span class="badge badge-{{ $project->status }}">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span>
      <span class="badge badge-{{ $project->priority }}">{{ ucfirst($project->priority) }} Priority</span>
      @if($project->projectType)
        <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.78rem;color:#6b7280">
          <span style="width:8px;height:8px;border-radius:50%;background:{{ $project->projectType->color }}"></span>
          {{ $project->projectType->name }}
        </span>
      @endif
    </div>
  </div>
  <div style="display:flex;gap:8px">
    <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit Project
    </a>
    <a href="{{ route('invoices.create') }}?project_id={{ $project->id }}" class="btn btn-primary">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Create Invoice
    </a>
  </div>
</div>

{{-- Finance summary bar --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px">
  <div class="stat-card" style="padding:16px">
    <div style="font-size:0.7rem;font-weight:700;color:#8b5cf6;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Estimated</div>
    <div style="font-size:1.4rem;font-weight:800;color:#1e1b4b">{{ $activeCompany->currency_symbol }}{{ number_format($project->estimated_amount ?? 0, 0) }}</div>
  </div>
  <div class="stat-card" style="padding:16px">
    <div style="font-size:0.7rem;font-weight:700;color:#10b981;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Received</div>
    <div style="font-size:1.4rem;font-weight:800;color:#1e1b4b">{{ $activeCompany->currency_symbol }}{{ number_format($project->getTotalReceivedAttribute(), 0) }}</div>
  </div>
  <div class="stat-card" style="padding:16px">
    <div style="font-size:0.7rem;font-weight:700;color:#ef4444;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Expenses</div>
    <div style="font-size:1.4rem;font-weight:800;color:#1e1b4b">{{ $activeCompany->currency_symbol }}{{ number_format($project->getTotalExpenseAttribute(), 0) }}</div>
  </div>
  <div class="stat-card" style="padding:16px">
    @php $pl = $project->getProfitLossAttribute(); @endphp
    <div style="font-size:0.7rem;font-weight:700;color:{{ $pl >= 0 ? '#10b981' : '#ef4444' }};text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">P&L</div>
    <div style="font-size:1.4rem;font-weight:800;color:{{ $pl >= 0 ? '#10b981' : '#ef4444' }}">{{ $pl >= 0 ? '+' : '' }}{{ $activeCompany->currency_symbol }}{{ number_format($pl, 0) }}</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
  {{-- Project Info --}}
  <div class="card">
    <div class="card-header"><span style="font-weight:700">Project Details</span></div>
    <div class="card-body">
      <dl style="display:grid;grid-template-columns:auto 1fr;gap:6px 16px;font-size:0.875rem">
        <dt style="color:#8b5cf6;font-weight:600;white-space:nowrap">Customer</dt>
        <dd>{{ $project->customer->name ?? '—' }}</dd>
        <dt style="color:#8b5cf6;font-weight:600">Location</dt>
        <dd>{{ $project->location ?? '—' }}</dd>
        <dt style="color:#8b5cf6;font-weight:600">Start</dt>
        <dd>{{ $project->start_date ? $project->start_date->format('d M Y') : '—' }}</dd>
        <dt style="color:#8b5cf6;font-weight:600">End</dt>
        <dd>{{ $project->end_date ? $project->end_date->format('d M Y') : '—' }}</dd>
        <dt style="color:#8b5cf6;font-weight:600">Lead By</dt>
        <dd>{{ $project->leadUser->name ?? '—' }}</dd>
      </dl>
      @if($project->scope_of_work)
      <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6">
        <div style="font-size:0.75rem;font-weight:700;color:#8b5cf6;margin-bottom:6px">Scope of Work</div>
        <div style="font-size:0.875rem;color:#6b7280;white-space:pre-wrap">{{ $project->scope_of_work }}</div>
      </div>
      @endif
    </div>
  </div>

  {{-- Tasks --}}
  <div class="card">
    <div class="card-header">
      <span style="font-weight:700">Tasks ({{ $project->tasks->count() }})</span>
      <button onclick="document.getElementById('addTaskModal').style.display='flex'" class="btn btn-secondary btn-sm">+ Task</button>
    </div>
    <div style="max-height:280px;overflow-y:auto">
      @forelse($project->tasks->sortBy('status') as $task)
      <div style="display:flex;align-items:center;gap:10px;padding:10px 20px;border-bottom:1px solid #f3f4f6">
        <form method="POST" action="{{ route('tasks.update', [$project, $task]) }}">
          @csrf @method('PUT')
          <input type="hidden" name="status" value="{{ $task->status === 'completed' ? 'pending' : 'completed' }}">
          <input type="hidden" name="title" value="{{ $task->title }}">
          <input type="hidden" name="priority" value="{{ $task->priority }}">
          <button type="submit" style="background:none;border:none;cursor:pointer;padding:0;width:20px;height:20px;border-radius:50%;border:2px solid {{ $task->status === 'completed' ? '#10b981' : '#d1d5db' }};background:{{ $task->status === 'completed' ? '#10b981' : 'transparent' }};display:flex;align-items:center;justify-content:center">
            @if($task->status === 'completed')
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            @endif
          </button>
        </form>
        <div style="flex:1;min-width:0">
          <div style="font-size:0.85rem;{{ $task->status === 'completed' ? 'text-decoration:line-through;color:#9ca3af' : 'color:#1e1b4b;font-weight:500' }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ $task->title }}
          </div>
          @if($task->due_date)
            <div style="font-size:0.72rem;color:{{ $task->due_date->isPast() && $task->status !== 'completed' ? '#ef4444' : '#9ca3af' }}">
              Due: {{ $task->due_date->format('d M') }}
            </div>
          @endif
        </div>
        <span class="badge badge-{{ $task->priority }}" style="font-size:0.65rem">{{ ucfirst($task->priority) }}</span>
      </div>
      @empty
      <div style="text-align:center;color:#9ca3af;padding:20px;font-size:0.85rem">No tasks yet.</div>
      @endforelse
    </div>
  </div>
</div>

{{-- Finance entries --}}
<div class="card mb-5">
  <div class="card-header">
    <span style="font-weight:700">Finance Entries</span>
    <a href="{{ route('finance.create', $project) }}" class="btn btn-secondary btn-sm">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Entry
    </a>
  </div>
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr><th>Date</th><th>Type</th><th>Description</th><th>Payment</th><th>Credit</th><th>Debit</th><th></th></tr>
      </thead>
      <tbody>
        @forelse($project->financeEntries as $entry)
        <tr>
          <td style="font-size:0.85rem">{{ $entry->date->format('d M Y') }}</td>
          <td>{{ $entry->entryType->name ?? '—' }}</td>
          <td>{{ $entry->remarks ?? '—' }}</td>
          <td>{{ $entry->paymentType->name ?? '—' }}</td>
          <td style="color:#10b981;font-weight:600">{{ $entry->type === 'credit' ? $activeCompany->currency_symbol.number_format($entry->amount, 0) : '' }}</td>
          <td style="color:#ef4444;font-weight:600">{{ $entry->type === 'debit' ? $activeCompany->currency_symbol.number_format($entry->amount, 0) : '' }}</td>
          <td>
            <form method="POST" action="{{ route('finance.destroy', [$project, $entry]) }}"
                  onsubmit="return confirm('Delete this entry?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-xs" title="Delete entry">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#9ca3af;padding:20px">No finance entries yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Files --}}
<div class="card mb-5">
  <div class="card-header">
    <span style="font-weight:700">Files ({{ $project->files->count() }})</span>
    <form method="POST" action="{{ route('projects.files.upload', $project) }}" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center">
      @csrf
      <input type="file" name="files[]" multiple style="font-size:0.8rem">
      <button type="submit" class="btn btn-secondary btn-sm">Upload</button>
    </form>
  </div>
  @if($project->files->isNotEmpty())
  <div style="padding:16px 20px;display:flex;flex-wrap:wrap;gap:12px">
    @foreach($project->files as $file)
    <div style="display:flex;align-items:center;gap:8px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:8px 12px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      <div>
        <a href="{{ $file->getUrlAttribute() }}" target="_blank" style="font-size:0.82rem;font-weight:600;color:#4f46e5;text-decoration:none">
          {{ Str::limit($file->original_name, 30) }}
        </a>
        <div style="font-size:0.72rem;color:#9ca3af">{{ $file->getFormattedSizeAttribute() }}</div>
      </div>
      <form method="POST" action="{{ route('projects.files.delete', [$project, $file]) }}">
        @csrf @method('DELETE')
        <button type="submit" style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </form>
    </div>
    @endforeach
  </div>
  @endif
</div>

{{-- Add Task Modal --}}
<div id="addTaskModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:16px;padding:28px;width:440px;box-shadow:0 20px 60px rgba(0,0,0,0.2)">
    <h3 style="font-size:1.1rem;font-weight:800;color:#1e1b4b;margin-bottom:20px">Add Task</h3>
    <form method="POST" action="{{ route('tasks.store', $project) }}">
      @csrf
      <div style="margin-bottom:14px">
        <label class="form-label">Title <span style="color:#ef4444">*</span></label>
        <input type="text" name="title" class="form-control" required>
      </div>
      <div style="margin-bottom:14px">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="2"></textarea>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
        <div>
          <label class="form-label">Assigned To</label>
          <select name="assigned_to" class="form-control">
            <option value="">— None —</option>
            @foreach($users as $u)
              <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Due Date</label>
          <input type="date" name="due_date" class="form-control">
        </div>
      </div>
      <div style="margin-bottom:20px">
        <label class="form-label">Priority</label>
        <select name="priority" class="form-control">
          <option value="medium">Medium</option>
          <option value="high">High</option>
          <option value="low">Low</option>
        </select>
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary">Add Task</button>
        <button type="button" onclick="document.getElementById('addTaskModal').style.display='none'" class="btn btn-secondary">Cancel</button>
      </div>
    </form>
  </div>
</div>
@endsection
