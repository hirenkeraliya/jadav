@extends('layouts.app')
@section('title', $staff->name)
@section('breadcrumb')
  <a href="{{ route('payroll.staff.index') }}" style="color:#8b5cf6;text-decoration:none">Staff</a> /
  {{ $staff->name }}
@endsection

@section('content')

@include('partials.flash')

<div class="page-header">
  <div>
    <h1 class="page-title">{{ $staff->name }}</h1>
    <p class="page-subtitle">{{ $staff->designation ?? 'Staff Member' }}</p>
  </div>
  <div style="display:flex;gap:8px">
    <a href="{{ route('payroll.entries.create', ['staff_id' => $staff->id]) }}" class="btn btn-primary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Time Entry
    </a>
    <a href="{{ route('payroll.staff.edit', $staff) }}" class="btn btn-secondary">Edit</a>
  </div>
</div>

{{-- Staff Info Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:24px">
  <div class="card" style="text-align:center;padding:20px">
    <div style="font-size:0.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Hourly Rate</div>
    <div style="font-size:1.6rem;font-weight:700;color:#4f46e5">{{ number_format($staff->hourly_rate, 2) }}</div>
  </div>
  <div class="card" style="text-align:center;padding:20px">
    <div style="font-size:0.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Total Entries</div>
    <div style="font-size:1.6rem;font-weight:700;color:#0891b2">{{ $entries->total() }}</div>
  </div>
  <div class="card" style="text-align:center;padding:20px">
    <div style="font-size:0.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px">Status</div>
    <div style="margin-top:6px"><span class="badge badge-{{ $staff->status }}">{{ ucfirst($staff->status) }}</span></div>
  </div>
  @if($staff->email || $staff->mobile)
  <div class="card" style="padding:20px">
    <div style="font-size:0.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Contact</div>
    @if($staff->email)<div style="font-size:0.85rem">{{ $staff->email }}</div>@endif
    @if($staff->mobile)<div style="font-size:0.85rem;color:#6b7280">{{ $staff->mobile }}</div>@endif
  </div>
  @endif
</div>

{{-- Time Entries --}}
<div class="card">
  <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center">
    <h3 style="font-size:1rem;font-weight:600;margin:0">Time Entries</h3>
    <a href="{{ route('payroll.entries.index', ['staff_id' => $staff->id]) }}" class="btn btn-secondary btn-sm">View All</a>
  </div>
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Start</th>
          <th>End</th>
          <th>Hours</th>
          <th>Notes</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($entries as $entry)
        <tr>
          <td style="font-weight:500">{{ $entry->entry_date->format('d M Y') }}</td>
          <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $entry->start_time)->format('h:i A') }}</td>
          <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $entry->end_time)->format('h:i A') }}</td>
          <td style="font-weight:600;color:#0891b2">{{ number_format($entry->hours, 2) }} hrs</td>
          <td style="color:#6b7280;font-size:0.85rem">{{ $entry->notes ?? '—' }}</td>
          <td>
            <div style="display:flex;gap:6px;justify-content:flex-end">
              <a href="{{ route('payroll.entries.edit', $entry) }}" class="btn btn-secondary btn-xs">Edit</a>
              <form method="POST" action="{{ route('payroll.entries.destroy', $entry) }}"
                    onsubmit="return confirm('Delete this time entry?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-xs">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align:center;padding:32px;color:#9ca3af">
            No time entries yet.
            <a href="{{ route('payroll.entries.create', ['staff_id' => $staff->id]) }}" style="color:#6366f1;font-weight:600">Add one →</a>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($entries->hasPages())
  <div style="padding:16px 20px;border-top:1px solid #f3f4f6">
    {{ $entries->links() }}
  </div>
  @endif
</div>
@endsection
