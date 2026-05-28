<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
  * { margin:0;padding:0;box-sizing:border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1e1b4b; background:#fff; }
  .page { padding: 36px 40px; }
  .header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:28px; padding-bottom:18px; border-bottom:3px solid {{ $company->primary_color ?? '#6366f1' }}; }
  .company-name { font-size:20px; font-weight:800; color:{{ $company->primary_color ?? '#6366f1' }}; }
  .company-details { font-size:9.5px; color:#6b7280; margin-top:4px; line-height:1.6; }
  .doc-title h1 { font-size:22px; font-weight:800; color:{{ $company->primary_color ?? '#6366f1' }}; text-transform:uppercase; letter-spacing:2px; }
  .doc-code { font-size:13px; font-weight:700; color:#1e1b4b; margin-top:4px; }
  .doc-meta { font-size:9.5px; color:#6b7280; margin-top:2px; }
  .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:1px; }
  .badge-pending { background:#ede9fe; color:#4c1d95; }
  .badge-running { background:#d1fae5; color:#065f46; }
  .badge-on_hold { background:#ffedd5; color:#9a3412; }
  .badge-delayed { background:#fee2e2; color:#991b1b; }
  .badge-completed { background:#dbeafe; color:#1e40af; }
  .badge-invoiced { background:#f5f3ff; color:#5b21b6; }
  .badge-cancelled { background:#f3f4f6; color:#6b7280; }
  .badge-high { background:#fee2e2; color:#991b1b; }
  .badge-medium { background:#fef3c7; color:#92400e; }
  .badge-low { background:#f0fdf4; color:#166534; }
  .section-title { font-size:8px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:{{ $company->primary_color ?? '#6366f1' }}; margin-bottom:8px; padding-bottom:4px; border-bottom:1px solid #ede9fe; }
  .two-col { display:flex; justify-content:space-between; margin-bottom:20px; }
  .col { width:48%; }
  .field-row { display:flex; margin-bottom:5px; font-size:10px; }
  .field-label { width:110px; font-weight:700; color:#6b7280; flex-shrink:0; }
  .field-value { color:#1e1b4b; }
  .text-block { font-size:10px; color:#374151; line-height:1.6; white-space:pre-wrap; padding:8px 10px; background:#f9fafb; border-left:2px solid {{ $company->primary_color ?? '#6366f1' }}; border-radius:2px; margin-bottom:16px; }
  .notes-block { font-size:10px; color:#78350f; line-height:1.6; white-space:pre-wrap; padding:8px 10px; background:#fffbeb; border-left:2px solid #f59e0b; border-radius:2px; margin-bottom:16px; }
  table { width:100%; border-collapse:collapse; margin-bottom:16px; }
  thead tr { background:{{ $company->primary_color ?? '#6366f1' }}; }
  thead th { color:#fff; font-size:9px; font-weight:700; text-transform:uppercase; padding:7px 10px; text-align:left; letter-spacing:0.5px; }
  tbody tr:nth-child(even) { background:#f9fafb; }
  tbody td { padding:7px 10px; font-size:10px; border-bottom:1px solid #f3f4f6; }
  .finance-summary { display:flex; justify-content:space-between; margin-bottom:20px; }
  .stat-box { width:23%; text-align:center; padding:10px 6px; border:1px solid #e5e7eb; border-radius:6px; }
  .stat-label { font-size:8px; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin-bottom:4px; }
  .stat-value { font-size:14px; font-weight:800; color:#1e1b4b; }
  .custom-grid { display:flex; flex-wrap:wrap; gap:0; margin-bottom:16px; }
  .custom-item { width:33%; padding:6px 8px; border-bottom:1px solid #f3f4f6; }
  .custom-label { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#8b5cf6; }
  .custom-value { font-size:10px; color:#1e1b4b; margin-top:2px; }
  .footer { border-top:1px solid #e5e7eb; padding-top:10px; text-align:center; font-size:9px; color:#9ca3af; margin-top:20px; }
</style>
</head>
<body>
<div class="page">

  {{-- Header --}}
  <div class="header">
    <div>
      @if($company->logo)
        <img src="{{ public_path('storage/'.$company->logo) }}" alt="{{ $company->name }}" style="max-height:55px;max-width:160px;margin-bottom:4px">
      @else
        <div class="company-name">{{ $company->name }}</div>
      @endif
      <div class="company-details">
        {{ $company->address }}<br>
        @if($company->phone) {{ $company->phone }} · @endif {{ $company->email }}<br>
        @if($company->tax_number) {{ $company->tax_label ?? 'Tax' }}: {{ $company->tax_number }} @endif
      </div>
    </div>
    <div style="text-align:right">
      <div class="doc-title"><h1>Project</h1></div>
      <div class="doc-code">{{ $project->project_code }}</div>
      <div class="doc-meta">Generated: {{ now()->format('d M Y') }}</div>
      <div style="margin-top:6px">
        <span class="badge badge-{{ $project->status }}">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span>
        &nbsp;
        <span class="badge badge-{{ $project->priority }}">{{ ucfirst($project->priority) }} Priority</span>
      </div>
    </div>
  </div>

  {{-- Project name --}}
  <div style="font-size:17px;font-weight:800;color:#1e1b4b;margin-bottom:18px">{{ $project->name }}</div>

  {{-- Finance summary --}}
  <div class="finance-summary">
    <div class="stat-box">
      <div class="stat-label" style="color:#10b981">Received</div>
      <div class="stat-value">{{ $company->currency_symbol }}{{ number_format($totalReceived, 0) }}</div>
    </div>
    <div class="stat-box">
      <div class="stat-label" style="color:#ef4444">Expenses</div>
      <div class="stat-value">{{ $company->currency_symbol }}{{ number_format($totalExpense, 0) }}</div>
    </div>
    <div class="stat-box">
      @php $pl = $totalReceived - $totalExpense; @endphp
      <div class="stat-label" style="color:{{ $pl >= 0 ? '#10b981' : '#ef4444' }}">P&L</div>
      <div class="stat-value" style="color:{{ $pl >= 0 ? '#10b981' : '#ef4444' }}">{{ $pl >= 0 ? '+' : '' }}{{ $company->currency_symbol }}{{ number_format($pl, 0) }}</div>
    </div>
  </div>

  {{-- Details --}}
  <div class="two-col" style="margin-bottom:20px">
    <div class="col">
      <div class="section-title">Project Details</div>
      <div class="field-row"><span class="field-label">Customer</span><span class="field-value">{{ $project->customer->name ?? '—' }}{{ $project->customer->organization ? ' ('.$project->customer->organization.')' : '' }}</span></div>
      <div class="field-row"><span class="field-label">Project Types</span><span class="field-value">{{ $project->projectTypes->pluck('name')->join(', ') ?: '—' }}</span></div>
      <div class="field-row"><span class="field-label">Lead By</span><span class="field-value">{{ $project->leadBy->name ?? '—' }}</span></div>
      <div class="field-row"><span class="field-label">Start Date</span><span class="field-value">{{ $project->start_date ? $project->start_date->format('d M Y') : '—' }}</span></div>
      <div class="field-row"><span class="field-label">End Date</span><span class="field-value">{{ $project->end_date ? $project->end_date->format('d M Y') : '—' }}</span></div>
    </div>
    <div class="col">
      <div class="section-title">Location</div>
      <div class="field-row"><span class="field-label">City / Location</span><span class="field-value">{{ $project->location ?: '—' }}</span></div>
      @if($project->site_address)
      <div class="field-row"><span class="field-label">Site Address</span><span class="field-value" style="white-space:pre-wrap">{{ $project->site_address }}</span></div>
      @endif
      @if($project->quotation)
      <div class="field-row" style="margin-top:8px"><span class="field-label">Quotation Ref.</span><span class="field-value">{{ $project->quotation->quotation_number }}</span></div>
      @endif
    </div>
  </div>

  {{-- Scope of work --}}
  @if($project->scope_of_work)
  <div class="section-title">Scope of Work</div>
  <div class="text-block">{{ $project->scope_of_work }}</div>
  @endif

  {{-- Internal notes --}}
  @if($project->internal_notes)
  <div class="section-title">Internal Notes</div>
  <div class="notes-block">{{ $project->internal_notes }}</div>
  @endif

  {{-- Custom fields --}}
  @if($customFields->isNotEmpty())
  <div class="section-title">Custom Fields</div>
  <div class="custom-grid">
    @foreach($customFields as $field)
    @php $cfv = $customValues[$field->id] ?? null; $val = $cfv?->value; @endphp
    <div class="custom-item">
      <div class="custom-label">{{ $field->label }}</div>
      <div class="custom-value">
        @if($val === null || $val === '') —
        @elseif($field->type === 'toggle') {{ $val ? 'Yes' : 'No' }}
        @elseif($field->type === 'multiselect') {{ implode(', ', json_decode($val, true) ?? []) }}
        @else {{ $val }}
        @endif
      </div>
    </div>
    @endforeach
  </div>
  @endif

  {{-- Tasks --}}
  @if($project->tasks->isNotEmpty())
  <div class="section-title">Tasks</div>
  <table>
    <thead>
      <tr><th>Title</th><th>Assigned To</th><th>Due Date</th><th>Priority</th><th>Status</th></tr>
    </thead>
    <tbody>
      @foreach($project->tasks as $task)
      <tr>
        <td>{{ $task->title }}</td>
        <td>{{ $task->assignee->name ?? '—' }}</td>
        <td>{{ $task->due_date ? $task->due_date->format('d M Y') : '—' }}</td>
        <td>{{ ucfirst($task->priority) }}</td>
        <td>{{ ucfirst(str_replace('_',' ',$task->status)) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  {{-- Finance entries --}}
  @if($financeEntries->isNotEmpty())
  <div class="section-title">Finance Entries</div>
  <table>
    <thead>
      <tr><th>Date</th><th>Type</th><th>Description</th><th>Payment</th><th style="text-align:right">Credit</th><th style="text-align:right">Debit</th></tr>
    </thead>
    <tbody>
      @foreach($financeEntries as $entry)
      <tr>
        <td>{{ $entry->date->format('d M Y') }}</td>
        <td>{{ $entry->entryType->name ?? '—' }}</td>
        <td>{{ $entry->remarks ?? '—' }}</td>
        <td>{{ $entry->paymentType->name ?? '—' }}</td>
        <td style="text-align:right;color:#10b981">{{ $entry->type === 'credit' ? $company->currency_symbol.number_format($entry->amount, 0) : '' }}</td>
        <td style="text-align:right;color:#ef4444">{{ $entry->type === 'debit' ? $company->currency_symbol.number_format($entry->amount, 0) : '' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  {{-- Footer --}}
  <div class="footer">
    {{ $company->name }} &mdash; {{ $project->project_code }} &mdash; Generated on {{ now()->format('d M Y, h:i A') }}
  </div>

</div>
</body>
</html>
