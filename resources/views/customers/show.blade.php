@extends('layouts.app')
@section('title', $customer->name)
@section('breadcrumb')
  <a href="{{ route('customers.index') }}" style="color:#8b5cf6;text-decoration:none">Customers</a> /
  {{ $customer->name }}
@endsection

@section('content')
<div class="page-header">
  <div>
    @if($customer->customer_code)
    <div style="margin-bottom:8px">
      <span style="display:inline-block;background:#ede9fe;color:#6d28d9;font-size:1.5rem;font-weight:800;letter-spacing:0.06em;padding:6px 18px;border-radius:10px">{{ $customer->customer_code }}</span>
    </div>
    @endif
    <h1 class="page-title">{{ $customer->name }}</h1>
    <p class="page-subtitle">{{ $customer->organization ?? 'Individual Client' }}</p>
  </div>
  <div style="display:flex;gap:8px">
    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-secondary">Edit</a>
    <a href="{{ route('quotations.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary">New Quotation</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:20px">
  {{-- Info card --}}
  <div>
    <div class="card mb-4">
      <div class="card-header">
        <span style="font-weight:700">Contact Details</span>
        <span class="badge badge-{{ $customer->status }}">{{ ucfirst($customer->status) }}</span>
      </div>
      <div class="card-body">
        @if($customer->email)
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;font-size:0.875rem">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <a href="mailto:{{ $customer->email }}" style="color:#6366f1">{{ $customer->email }}</a>
        </div>
        @endif
        @if($customer->mobile)
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;font-size:0.875rem">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.99 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.92 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          {{ $customer->mobile }}
        </div>
        @endif
        @if($customer->address)
        <div style="display:flex;gap:8px;margin-bottom:10px;font-size:0.875rem;color:#6b7280">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" style="flex-shrink:0;margin-top:2px"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          {{ $customer->address }}
        </div>
        @endif
        @if($customer->source)
        <div style="font-size:0.8rem;color:#9ca3af;margin-top:8px">Source: {{ $customer->source }}</div>
        @endif
        @if($customer->notes)
        <div style="margin-top:12px;padding-top:12px;border-top:1px solid #f3f4f6;font-size:0.85rem;color:#6b7280">
          {{ $customer->notes }}
        </div>
        @endif
      </div>
    </div>

  </div>

  {{-- Projects --}}
  <div>
    <div class="card mb-4">
      <div class="card-header">
        <span style="font-weight:700">Projects ({{ $projects->total() }})</span>
        <a href="{{ route('projects.create') }}?customer_id={{ $customer->id }}" class="btn btn-secondary btn-sm">+ Project</a>
      </div>
      <div class="table-wrapper">
        <table class="table">
          <thead><tr><th>Code</th><th>Name</th><th>Status</th><th>End Date</th><th style="text-align:right">Received</th><th style="text-align:right">Expense</th></tr></thead>
          <tbody>
            @forelse($projects as $proj)
            <tr>
              <td><span style="background:#ede9fe;color:#6d28d9;font-size:0.82rem;font-weight:800;padding:2px 8px;border-radius:6px">{{ $proj->project_code }}</span></td>
              <td><a href="{{ route('projects.show', $proj) }}" style="font-weight:600;color:#4f46e5;text-decoration:none">{{ $proj->name }}</a></td>
              <td><span class="badge badge-{{ $proj->status }}">{{ ucfirst(str_replace('_',' ',$proj->status)) }}</span></td>
              <td style="font-size:0.85rem;color:#6b7280">{{ $proj->end_date ? $proj->end_date->format('d M Y') : '—' }}</td>
              <td style="text-align:right;color:#10b981;font-weight:600;font-size:0.85rem">{{ $activeCompany->currency_symbol }}{{ number_format($proj->total_received ?? 0, 0) }}</td>
              <td style="text-align:right;color:#ef4444;font-weight:600;font-size:0.85rem">{{ $activeCompany->currency_symbol }}{{ number_format($proj->total_expense ?? 0, 0) }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:20px">No projects yet</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($projects->hasPages())
      <div style="padding:14px 20px;border-top:1px solid #f3f4f6">
        {{ $projects->links() }}
      </div>
      @endif
    </div>

  </div>
</div>
@endsection
