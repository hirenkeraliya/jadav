@extends('layouts.app')
@section('title', 'Payroll Report')
@section('breadcrumb', 'Payroll / Monthly Report')

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Monthly Payroll Report</h1>
    <p class="page-subtitle">{{ $months[$month] }} {{ $year }}</p>
  </div>
  @if(count($report) > 0)
  <button onclick="window.print()" class="btn btn-secondary">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
    Print / PDF
  </button>
  @endif
</div>

{{-- Filters --}}
<div class="card mb-5">
  <div class="card-body" style="padding:16px 20px">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
      <select name="month" class="form-control" style="max-width:150px">
        @foreach($months as $num => $name)
          <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
        @endforeach
      </select>
      <input type="number" name="year" class="form-control" style="max-width:100px"
             min="2020" max="2099" value="{{ $year }}">
      <select name="staff_id" class="form-control" style="max-width:220px">
        <option value="">All staff</option>
        @foreach($staffList as $member)
          <option value="{{ $member->id }}" {{ $staffId == $member->id ? 'selected' : '' }}>
            {{ $member->name }}
          </option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Generate Report</button>
    </form>
  </div>
</div>

@if(count($report) > 0)

{{-- Summary Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px">
  <div class="card" style="text-align:center;padding:20px">
    <div style="font-size:0.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Staff Count</div>
    <div style="font-size:1.8rem;font-weight:700;color:#4f46e5">{{ count($report) }}</div>
  </div>
  <div class="card" style="text-align:center;padding:20px">
    <div style="font-size:0.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Total Hours</div>
    <div style="font-size:1.8rem;font-weight:700;color:#0891b2">{{ number_format($grandTotalHours, 2) }}</div>
  </div>
  <div class="card" style="text-align:center;padding:20px">
    <div style="font-size:0.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Total Payable</div>
    <div style="font-size:1.8rem;font-weight:700;color:#059669">{{ number_format($grandTotalPayable, 2) }}</div>
  </div>
</div>

{{-- Summary Table --}}
<div class="card mb-6">
  <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6">
    <h3 style="font-size:1rem;font-weight:600;margin:0">Staff Summary</h3>
  </div>
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Staff</th>
          <th>Designation</th>
          <th>Hourly Rate</th>
          <th>Total Hours</th>
          <th style="text-align:right">Payable Amount</th>
        </tr>
      </thead>
      <tbody>
        @foreach($report as $row)
        <tr>
          <td style="font-weight:600">{{ $row['staff']->name }}</td>
          <td style="color:#6b7280">{{ $row['staff']->designation ?? '—' }}</td>
          <td>{{ number_format($row['staff']->hourly_rate, 2) }}</td>
          <td style="font-weight:600;color:#0891b2">{{ number_format($row['total_hours'], 2) }} hrs</td>
          <td style="text-align:right;font-weight:700;color:#059669">{{ number_format($row['payable_amount'], 2) }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="background:#f9fafb;font-weight:700">
          <td colspan="3" style="text-align:right;color:#374151">Grand Total</td>
          <td style="color:#0891b2">{{ number_format($grandTotalHours, 2) }} hrs</td>
          <td style="text-align:right;color:#059669">{{ number_format($grandTotalPayable, 2) }}</td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

{{-- Detailed Entries per Staff --}}
@foreach($report as $row)
<div class="card mb-5">
  <div style="padding:14px 20px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center">
    <div>
      <span style="font-weight:700;font-size:1rem">{{ $row['staff']->name }}</span>
      @if($row['staff']->designation)
        <span style="color:#6b7280;font-size:0.85rem;margin-left:8px">{{ $row['staff']->designation }}</span>
      @endif
    </div>
    <div style="text-align:right">
      <div style="font-size:0.75rem;color:#6b7280">Total Hrs: <strong>{{ number_format($row['total_hours'], 2) }}</strong></div>
      <div style="font-size:0.75rem;color:#059669">Payable: <strong>{{ number_format($row['payable_amount'], 2) }}</strong></div>
    </div>
  </div>
  <div class="table-wrapper">
    <table class="table" style="font-size:0.875rem">
      <thead>
        <tr>
          <th>Date</th>
          <th>Start</th>
          <th>End</th>
          <th>Hours</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        @foreach($row['entries'] as $entry)
        <tr>
          <td>{{ $entry->entry_date->format('d M Y (D)') }}</td>
          <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $entry->start_time)->format('h:i A') }}</td>
          <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $entry->end_time)->format('h:i A') }}</td>
          <td style="font-weight:600;color:#0891b2">{{ number_format($entry->hours, 2) }} hrs</td>
          <td style="color:#6b7280">{{ $entry->notes ?? '—' }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="background:#f0fdf4;font-weight:700">
          <td colspan="3" style="text-align:right;color:#374151">Subtotal</td>
          <td style="color:#0891b2">{{ number_format($row['total_hours'], 2) }} hrs</td>
          <td style="color:#059669">
            {{ number_format($row['staff']->hourly_rate, 2) }} × {{ number_format($row['total_hours'], 2) }}
            = <strong>{{ number_format($row['payable_amount'], 2) }}</strong>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@endforeach

@else
<div class="card" style="text-align:center;padding:60px 20px;color:#9ca3af">
  <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 16px;opacity:0.4"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/><line x1="9" y1="15" x2="13" y2="15"/></svg>
  <p style="font-size:1rem;font-weight:500">No entries found for {{ $months[$month] }} {{ $year }}.</p>
  <p style="font-size:0.875rem;margin-top:4px">Select a different month/year or <a href="{{ route('payroll.entries.create') }}" style="color:#6366f1;font-weight:600">add time entries</a>.</p>
</div>
@endif

@push('styles')
<style>
@media print {
  .sidebar, .topbar, .btn, form.card { display: none !important; }
  .card { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
}
</style>
@endpush
@endsection
