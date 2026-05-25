@extends('layouts.app')
@section('title', 'Finance Report')
@section('breadcrumb')
  <a href="{{ route('reports.index') }}" style="color:#8b5cf6;text-decoration:none">Reports</a> / Finance
@endsection

@section('content')
<div class="page-header">
  <h1 class="page-title">Finance Report</h1>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('reports.finance') }}" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;margin-bottom:24px">
  <div>
    <label class="form-label">From</label>
    <input type="date" name="from" class="form-control" value="{{ $from }}">
  </div>
  <div>
    <label class="form-label">To</label>
    <input type="date" name="to" class="form-control" value="{{ $to }}">
  </div>
  <div>
    <label class="form-label">Group By</label>
    <select name="group_by" class="form-control">
      <option value="project" {{ $groupBy == 'project' ? 'selected' : '' }}>Project</option>
      <option value="month" {{ $groupBy == 'month' ? 'selected' : '' }}>Month</option>
      <option value="type" {{ $groupBy == 'type' ? 'selected' : '' }}>Entry Type</option>
    </select>
  </div>
  <button type="submit" class="btn btn-primary">Apply</button>
  <a href="{{ route('reports.finance') }}" class="btn btn-secondary">Reset</a>
</form>

{{-- Summary cards --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
  <div class="stat-card">
    <div class="stat-value" style="color:#10b981">{{ $company->currency_symbol }}{{ number_format($summary['total_credit'] ?? 0, 2) }}</div>
    <div class="stat-label">Total Income</div>
  </div>
  <div class="stat-card">
    <div class="stat-value" style="color:#ef4444">{{ $company->currency_symbol }}{{ number_format($summary['total_debit'] ?? 0, 2) }}</div>
    <div class="stat-label">Total Expense</div>
  </div>
  <div class="stat-card">
    <div class="stat-value" style="color:{{ ($summary['net'] ?? 0) >= 0 ? '#6366f1' : '#ef4444' }}">
      {{ $company->currency_symbol }}{{ number_format(abs($summary['net'] ?? 0), 2) }}
    </div>
    <div class="stat-label">Net ({{ ($summary['net'] ?? 0) >= 0 ? 'Profit' : 'Loss' }})</div>
  </div>
  <div class="stat-card">
    <div class="stat-value">{{ $entries->count() }}</div>
    <div class="stat-label">Transactions</div>
  </div>
</div>

{{-- Grouped table --}}
@if($grouped->count())
<div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;margin-bottom:24px">
  {{-- Breakdown chart / table --}}
  <div class="card">
    <div class="card-header"><span style="font-weight:700">Breakdown by {{ ucfirst($groupBy) }}</span></div>
    <div class="table-wrapper">
      <table class="table" style="font-size:0.875rem">
        <thead><tr><th>{{ ucfirst($groupBy) }}</th><th style="text-align:right">Credit</th><th style="text-align:right">Debit</th><th style="text-align:right">Net</th></tr></thead>
        <tbody>
          @foreach($grouped as $row)
          <tr>
            <td style="font-weight:600">{{ $row->group_label ?? '—' }}</td>
            <td style="text-align:right;color:#10b981">{{ $company->currency_symbol }}{{ number_format($row->total_credit ?? 0, 2) }}</td>
            <td style="text-align:right;color:#ef4444">{{ $company->currency_symbol }}{{ number_format($row->total_debit ?? 0, 2) }}</td>
            <td style="text-align:right;font-weight:700;color:{{ ($row->total_credit - $row->total_debit) >= 0 ? '#6366f1' : '#ef4444' }}">
              {{ $company->currency_symbol }}{{ number_format($row->total_credit - $row->total_debit, 2) }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Visual bar chart --}}
  <div class="card">
    <div class="card-header"><span style="font-weight:700">Visual Overview</span></div>
    <div class="card-body">
      @php $maxVal = $grouped->max(fn($r) => max($r->total_credit, $r->total_debit)) ?: 1; @endphp
      @foreach($grouped->take(8) as $row)
      <div style="margin-bottom:10px">
        <div style="display:flex;justify-content:space-between;font-size:0.78rem;color:#6b7280;margin-bottom:3px">
          <span>{{ $row->group_label ?? '—' }}</span>
          <span>{{ $company->currency_symbol }}{{ number_format($row->total_credit - $row->total_debit, 0) }}</span>
        </div>
        <div style="background:#f3f4f6;border-radius:99px;height:8px;overflow:hidden">
          <div style="height:100%;background:{{ ($row->total_credit - $row->total_debit) >= 0 ? '#10b981' : '#ef4444' }};border-radius:99px;width:{{ min(100, abs($row->total_credit - $row->total_debit) / $maxVal * 100) }}%;transition:width .3s"></div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endif

{{-- All transactions --}}
<div class="card">
  <div class="card-header"><span style="font-weight:700">All Transactions</span></div>
  <div class="table-wrapper">
    <table class="table">
      <thead><tr><th>Date</th><th>Project</th><th>Type</th><th>Description</th><th>Credit</th><th>Debit</th></tr></thead>
      <tbody>
        @forelse($entries as $e)
        <tr>
          <td style="white-space:nowrap;color:#9ca3af;font-size:0.82rem">{{ $e->date->format('d M Y') }}</td>
          <td>{{ $e->project->name ?? '—' }}</td>
          <td>{{ $e->entryType->name ?? '—' }}</td>
          <td>{{ $e->remarks }}</td>
          <td style="color:#10b981;font-weight:600">{{ $e->type=='credit' ? $company->currency_symbol.number_format($e->amount,2) : '' }}</td>
          <td style="color:#ef4444;font-weight:600">{{ $e->type=='debit' ? $company->currency_symbol.number_format($e->amount,2) : '' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:24px">No transactions in selected range.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
