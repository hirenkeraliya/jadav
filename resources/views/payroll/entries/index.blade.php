@extends('layouts.app')
@section('title', 'Time Entries')
@section('breadcrumb', 'Payroll / Time Entries')

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Time Entries</h1>
    <p class="page-subtitle">{{ $entries->total() }} total entries</p>
  </div>
  @can('payroll.create')
  <a href="{{ route('payroll.entries.create') }}" class="btn btn-primary">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Add Entry
  </a>
  @endcan
</div>

@include('partials.flash')

{{-- Filters --}}
<div class="card mb-5">
  <div class="card-body" style="padding:16px 20px">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
      <select name="staff_id" class="form-control" style="max-width:220px">
        <option value="">All staff</option>
        @foreach($staffList as $member)
          <option value="{{ $member->id }}" {{ request('staff_id') == $member->id ? 'selected' : '' }}>
            {{ $member->name }}
          </option>
        @endforeach
      </select>
      <input type="date" name="date" class="form-control" style="max-width:160px"
             value="{{ request('date') }}">
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      @if(request('staff_id') || request('date'))
        <a href="{{ route('payroll.entries.index') }}" class="btn btn-secondary btn-sm">Clear</a>
      @endif
    </form>
  </div>
</div>

<div class="card">
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Staff</th>
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
          <td>
            <a href="{{ route('payroll.staff.show', $entry->staff) }}" style="font-weight:600;color:#4f46e5;text-decoration:none">
              {{ $entry->staff->name }}
            </a>
          </td>
          <td style="font-weight:500">{{ $entry->entry_date->format('d M Y') }}</td>
          <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $entry->start_time)->format('h:i A') }}</td>
          <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $entry->end_time)->format('h:i A') }}</td>
          <td style="font-weight:600;color:#0891b2">{{ number_format($entry->hours, 2) }} hrs</td>
          <td style="color:#6b7280;font-size:0.85rem">{{ $entry->notes ?? '—' }}</td>
          <td>
            <div style="display:flex;gap:6px;justify-content:flex-end">
              @can('payroll.edit')
              <a href="{{ route('payroll.entries.edit', $entry) }}" class="btn btn-secondary btn-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
              </a>
              @endcan
              @can('payroll.delete')
              <form method="POST" action="{{ route('payroll.entries.destroy', $entry) }}"
                    onsubmit="return confirm('Delete this time entry?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-xs">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg> Delete
                </button>
              </form>
              @endcan
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align:center;padding:48px;color:#9ca3af">
            No time entries found.
            <a href="{{ route('payroll.entries.create') }}" style="color:#6366f1;font-weight:600">Add the first one →</a>
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
