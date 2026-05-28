@extends('layouts.app')
@section('title', $project->name)
@section('breadcrumb')
  <a href="{{ route('projects.index') }}" style="color:#8b5cf6;text-decoration:none">Projects</a> /
  {{ $project->name }}
@endsection

@section('content')
<div class="page-header">
  <div>
    <div style="margin-bottom:8px">
      <span style="display:inline-block;background:#ede9fe;color:#6d28d9;font-size:1.5rem;font-weight:800;letter-spacing:0.06em;padding:6px 18px;border-radius:10px">{{ $project->project_code }}</span>
    </div>
    <h1 class="page-title">{{ $project->name }}</h1>
    <div style="display:flex;align-items:center;gap:8px;margin-top:6px;flex-wrap:wrap">

      {{-- Status: clickable dropdown for users with permission, static badge otherwise --}}
      @can('projects.change_status')
      @php
        $allStatuses = ['running','on_hold','delayed','completed','cancelled','quotation'];
        $statusColors = [
          'running'   => ['bg'=>'#d1fae5','color'=>'#065f46'],
          'on_hold'   => ['bg'=>'#ffedd5','color'=>'#9a3412'],
          'delayed'   => ['bg'=>'#fee2e2','color'=>'#991b1b'],
          'completed' => ['bg'=>'#dbeafe','color'=>'#1e40af'],
          'cancelled' => ['bg'=>'#f3f4f6','color'=>'#6b7280'],
          'quotation' => ['bg'=>'#fef3c7','color'=>'#92400e'],
        ];
      @endphp
      <div x-data="{ open: false }" style="position:relative">
        <button type="button" @click="open = !open" @keydown.escape.window="open = false"
                style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px 4px 12px;border-radius:20px;font-size:0.8rem;font-weight:700;border:2px solid {{ $statusColors[$project->status]['color'] ?? '#6b7280' }};background:{{ $statusColors[$project->status]['bg'] ?? '#f3f4f6' }};color:{{ $statusColors[$project->status]['color'] ?? '#6b7280' }};cursor:pointer">
          {{ ucfirst(str_replace('_',' ',$project->status)) }}
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" :style="open ? 'transform:rotate(180deg)' : ''"><polyline points="6 9 12 15 18 9"/></svg>
        </button>

        <div x-show="open" x-cloak @click.outside="open = false"
             style="position:absolute;top:calc(100% + 6px);left:0;z-index:50;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.12);min-width:180px;padding:6px">
          @foreach($allStatuses as $s)
          @if($s !== $project->status)
          <form method="POST" action="{{ route('projects.change-status', $project) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="{{ $s }}">
            <button type="submit" @click="open = false"
                    style="display:flex;align-items:center;gap:10px;width:100%;padding:8px 12px;background:none;border:none;cursor:pointer;border-radius:7px;font-size:0.85rem;color:#374151;text-align:left"
                    onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='none'">
              <span style="width:10px;height:10px;border-radius:50%;background:{{ $statusColors[$s]['color'] ?? '#6b7280' }};flex-shrink:0"></span>
              {{ ucfirst(str_replace('_',' ',$s)) }}
            </button>
          </form>
          @else
          <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:7px;background:#f9fafb;font-size:0.85rem;font-weight:700;color:{{ $statusColors[$s]['color'] ?? '#6b7280' }}">
            <span style="width:10px;height:10px;border-radius:50%;background:{{ $statusColors[$s]['color'] ?? '#6b7280' }};flex-shrink:0"></span>
            {{ ucfirst(str_replace('_',' ',$s)) }}
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-left:auto"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          @endif
          @endforeach
        </div>
      </div>
      @else
      <span class="badge badge-{{ $project->status }}">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span>
      @endcan

      <span class="badge badge-{{ $project->priority }}">{{ ucfirst($project->priority) }} Priority</span>
      @foreach($project->projectTypes as $pt)
        <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.78rem;color:#6b7280;background:#f3f4f6;padding:2px 8px;border-radius:20px">
          <span style="width:8px;height:8px;border-radius:50%;background:{{ $pt->color }}"></span>
          {{ $pt->name }}
        </span>
      @endforeach
    </div>
  </div>
  <div style="display:flex;gap:8px;align-items:center">
    @if($project->status === 'running')
      @can('projects.change_status')
      <a href="{{ route('projects.complete.create', $project) }}"
         style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#10b981;color:#fff;border:none;border-radius:8px;font-size:0.875rem;font-weight:700;text-decoration:none">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Complete Project
      </a>
      @endcan
    @endif
    @if($project->completion)
      <a href="{{ route('projects.completion.pdf', $project) }}" target="_blank" class="btn btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Invoice PDF
      </a>
    @endif
    <a href="{{ route('projects.pdf', $project) }}" target="_blank" class="btn btn-secondary">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> PDF
    </a>
    <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit Project
    </a>
  </div>
</div>

{{-- Finance summary bar --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px">
  <div class="stat-card" style="padding:16px">
    <div style="font-size:0.7rem;font-weight:700;color:#10b981;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Received</div>
    <div style="font-size:1.4rem;font-weight:800;color:#1e1b4b">{{ $activeCompany->currency_symbol }}{{ number_format($totalReceived, 0) }}</div>
  </div>
  <div class="stat-card" style="padding:16px">
    <div style="font-size:0.7rem;font-weight:700;color:#ef4444;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Expenses</div>
    <div style="font-size:1.4rem;font-weight:800;color:#1e1b4b">{{ $activeCompany->currency_symbol }}{{ number_format($totalExpense, 0) }}</div>
  </div>
  <div class="stat-card" style="padding:16px">
    <div style="font-size:0.7rem;font-weight:700;color:{{ $profitLoss >= 0 ? '#10b981' : '#ef4444' }};text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">P&L</div>
    <div style="font-size:1.4rem;font-weight:800;color:{{ $profitLoss >= 0 ? '#10b981' : '#ef4444' }}">{{ $profitLoss >= 0 ? '+' : '' }}{{ $activeCompany->currency_symbol }}{{ number_format($profitLoss, 0) }}</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
  {{-- Project Details --}}
  <div class="card">
    <div class="card-header"><span style="font-weight:700">Project Details</span></div>
    <div class="card-body">
      <dl style="display:grid;grid-template-columns:auto 1fr;gap:8px 16px;font-size:0.875rem;align-items:start">

        <dt style="color:#8b5cf6;font-weight:600;white-space:nowrap">Client</dt>
        <dd>
          @if($project->customer)
            <a href="{{ route('customers.show', $project->customer) }}" style="color:#4f46e5;text-decoration:none;font-weight:500">
              {{ $project->customer->name }}
            </a>
            @if($project->customer->organization)
              <span style="color:#6b7280"> · {{ $project->customer->organization }}</span>
            @endif
          @else
            —
          @endif
        </dd>

        <dt style="color:#8b5cf6;font-weight:600;white-space:nowrap">Lead By</dt>
        <dd>{{ $project->leadBy->name ?? '—' }}</dd>

        <dt style="color:#8b5cf6;font-weight:600;white-space:nowrap">Start Date</dt>
        <dd>{{ $project->start_date ? $project->start_date->format('d M Y') : '—' }}</dd>

        <dt style="color:#8b5cf6;font-weight:600;white-space:nowrap">End Date</dt>
        <dd>{{ $project->end_date ? $project->end_date->format('d M Y') : '—' }}</dd>

        <dt style="color:#8b5cf6;font-weight:600;white-space:nowrap">Location</dt>
        <dd>{{ $project->location ?: '—' }}</dd>

        @if($project->site_address)
        <dt style="color:#8b5cf6;font-weight:600;white-space:nowrap">Site Address</dt>
        <dd style="white-space:pre-wrap">{{ $project->site_address }}</dd>
        @endif

        @if($project->quotation)
        <dt style="color:#8b5cf6;font-weight:600;white-space:nowrap">Quotation</dt>
        <dd>
          <a href="{{ route('quotations.show', $project->quotation) }}" style="color:#4f46e5;text-decoration:none">
            {{ $project->quotation->quotation_number }}
          </a>
        </dd>
        @endif

      </dl>

      @if($project->internal_notes)
      <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6">
        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#f59e0b;margin-bottom:6px">Internal Notes</div>
        <div style="font-size:0.875rem;color:#374151;white-space:pre-wrap;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 12px">{{ $project->internal_notes }}</div>
      </div>
      @endif
    </div>
  </div>

  {{-- Scope of Work --}}
  <div class="card">
    <div class="card-header"><span style="font-weight:700">Scope of Work</span></div>
    <div class="card-body">
      @if($project->scope_of_work)
        <div style="font-size:0.875rem;color:#374151;white-space:pre-wrap;line-height:1.6">{{ $project->scope_of_work }}</div>
      @else
        <div style="text-align:center;color:#9ca3af;font-size:0.85rem;padding:20px 0">
          No scope of work defined.
          @can('projects.edit')
            <a href="{{ route('projects.edit', $project) }}" style="color:var(--color-primary);text-decoration:none">Add it</a>
          @endcan
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Extra / Less Work — 3rd box --}}
<div class="card mb-5">
  <div class="card-header"><span style="font-weight:700">Extra / Less Work</span></div>
  <div class="card-body">
    <form method="POST" action="{{ route('projects.update-work', $project) }}">
      @csrf @method('PATCH')
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <div>
          <label class="form-label" style="color:#10b981;font-weight:700">Extra Work</label>
          <textarea name="extra_work" class="form-control" rows="6"
                    placeholder="Describe any additional work…"
                    @cannot('projects.edit') readonly @endcannot>{{ old('extra_work', $project->extra_work) }}</textarea>
        </div>
        <div>
          <label class="form-label" style="color:#ef4444;font-weight:700">Less Work</label>
          <textarea name="less_work" class="form-control" rows="6"
                    placeholder="Describe any work removed or reduced…"
                    @cannot('projects.edit') readonly @endcannot>{{ old('less_work', $project->less_work) }}</textarea>
        </div>
      </div>
      @can('projects.edit')
      <div style="margin-top:14px;display:flex;justify-content:flex-end">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
      </div>
      @endcan
    </form>
  </div>
</div>

{{-- Tasks --}}
<div class="card mb-5">
  <div class="card-header">
    <span style="font-weight:700">Tasks ({{ $project->tasks->count() }})</span>
    <button onclick="document.getElementById('addTaskModal').style.display='flex'" class="btn btn-secondary btn-sm">+ Task</button>
  </div>
  <div style="max-height:340px;overflow-y:auto">
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

{{-- Custom Fields --}}
@if($customFields->isNotEmpty())
<div class="card" style="margin-bottom:20px">
  <div class="card-header"><span style="font-weight:700">Custom Fields</span></div>
  <div class="card-body">
    <dl style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;font-size:0.875rem">
      @foreach($customFields as $field)
      @php $cfv = $customValues[$field->id] ?? null; $val = $cfv?->value; @endphp
      <div style="background:#f9fafb;border-radius:8px;padding:12px">
        <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#8b5cf6;margin-bottom:4px">{{ $field->label }}</div>
        <div style="color:#1e1b4b">
          @if($val === null || $val === '')
            <span style="color:#9ca3af">—</span>
          @elseif($field->type === 'toggle')
            <span style="color:{{ $val ? '#10b981' : '#ef4444' }}">{{ $val ? 'Yes' : 'No' }}</span>
          @elseif($field->type === 'url')
            <a href="{{ $val }}" target="_blank" style="color:#4f46e5;word-break:break-all">{{ $val }}</a>
          @elseif($field->type === 'multiselect')
            @foreach(json_decode($val, true) ?? [] as $item)
              <span style="display:inline-block;background:#ede9fe;color:#6d28d9;border-radius:4px;padding:1px 7px;font-size:0.78rem;margin:2px">{{ $item }}</span>
            @endforeach
          @else
            {{ $val }}
          @endif
        </div>
      </div>
      @endforeach
    </dl>
  </div>
</div>
@endif

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
        @forelse($financeEntries as $entry)
        <tr>
          <td style="font-size:0.85rem">{{ $entry->date->format('d M Y') }}</td>
          <td>{{ $entry->entryType->name ?? '—' }}</td>
          <td>{{ $entry->remarks ?? '—' }}</td>
          <td>{{ $entry->paymentType->name ?? '—' }}</td>
          <td style="color:#10b981;font-weight:600">{{ $entry->type === 'credit' ? $activeCompany->currency_symbol.number_format($entry->amount, 0) : '' }}</td>
          <td style="color:#ef4444;font-weight:600">{{ $entry->type === 'debit' ? $activeCompany->currency_symbol.number_format($entry->amount, 0) : '' }}</td>
          <td>
            <div style="display:flex;gap:6px;justify-content:flex-end">
              <a href="{{ route('finance.edit', [$project, $entry]) }}" class="btn btn-secondary btn-xs" title="Edit entry">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <form method="POST" action="{{ route('finance.destroy', [$project, $entry]) }}"
                    onsubmit="return confirm('Delete this entry?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-xs" title="Delete entry">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#9ca3af;padding:20px">No finance entries yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Completion Invoice + Payment Tracking --}}
@if($project->completion)
@php $c = $project->completion; @endphp
<div class="card mb-5" style="border:2px solid {{ $c->payment_status === 'paid' ? '#10b981' : ($c->payment_status === 'partial' ? '#f59e0b' : '#ef4444') }}">
  <div class="card-header">
    <div style="display:flex;align-items:center;gap:10px">
      <span style="font-weight:700">Completion Invoice</span>
      <span style="font-size:0.78rem;color:#6b7280">{{ $c->invoice_number }}</span>
      @php
        $psBadge = match($c->payment_status) {
          'paid'    => ['bg'=>'#d1fae5','color'=>'#065f46','label'=>'Paid'],
          'partial' => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'Partial'],
          default   => ['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'Unpaid — Pending Collection'],
        };
      @endphp
      <span style="background:{{ $psBadge['bg'] }};color:{{ $psBadge['color'] }};font-size:0.75rem;font-weight:700;padding:3px 10px;border-radius:20px">{{ $psBadge['label'] }}</span>
    </div>
    <div style="display:flex;gap:8px">
      <a href="{{ route('projects.completion.edit', $project) }}" class="btn btn-secondary btn-sm">Edit Invoice</a>
      <a href="{{ route('projects.completion.pdf', $project) }}" target="_blank" class="btn btn-secondary btn-sm">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> PDF
      </a>
    </div>
  </div>

  <div style="padding:16px 20px">
    {{-- Line items --}}
    <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#8b5cf6;margin-bottom:10px">Line Items</div>
    <table style="width:100%;border-collapse:collapse;font-size:0.85rem">
      <thead>
        <tr style="border-bottom:1px solid #f3f4f6">
          <th style="text-align:left;padding:4px 0;font-weight:600;color:#6b7280">Description</th>
          <th style="text-align:right;padding:4px 0;font-weight:600;color:#6b7280">Qty</th>
          <th style="text-align:right;padding:4px 0;font-weight:600;color:#6b7280">Rate</th>
          <th style="text-align:right;padding:4px 0;font-weight:600;color:#6b7280">Amount</th>
        </tr>
      </thead>
      <tbody>
        @foreach($c->items as $item)
        <tr style="border-bottom:1px solid #f9fafb">
          <td style="padding:6px 0">{{ $item->description }}</td>
          <td style="text-align:right;padding:6px 0">{{ $item->qty }}</td>
          <td style="text-align:right;padding:6px 0">{{ $activeCompany->currency_symbol }}{{ number_format($item->rate, 0) }}</td>
          <td style="text-align:right;padding:6px 0;font-weight:600">{{ $activeCompany->currency_symbol }}{{ number_format($item->amount, 0) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- Totals (received is derived from credit finance entries) --}}
    <div style="display:flex;justify-content:flex-end;margin-top:14px">
      <div style="width:300px">
        <div style="display:flex;justify-content:space-between;padding-top:10px;border-top:2px solid #1e1b4b;font-weight:800">
          <span>Invoice Total</span>
          <span>{{ $activeCompany->currency_symbol }}{{ number_format($c->total, 0) }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:6px;font-size:0.85rem;color:#10b981;font-weight:600">
          <span>Received (from credit entries)</span>
          <span>- {{ $activeCompany->currency_symbol }}{{ number_format($c->paid_amount, 0) }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:6px;padding-top:8px;border-top:1px solid #f3f4f6;font-weight:800;color:#ef4444">
          <span>Due</span>
          <span>{{ $activeCompany->currency_symbol }}{{ number_format($c->due_amount, 0) }}</span>
        </div>
        <div style="margin-top:10px;font-size:0.72rem;color:#9ca3af;text-align:right">
          Add a <a href="{{ route('finance.create', $project) }}" style="color:var(--color-primary);text-decoration:none">credit finance entry</a> to record further payments.
        </div>
      </div>
    </div>
  </div>

  @if($c->notes)
  <div style="padding:12px 20px;border-top:1px solid #f3f4f6;font-size:0.85rem;color:#6b7280;background:#fafafa">
    {{ $c->notes }}
  </div>
  @endif
</div>
@endif

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
