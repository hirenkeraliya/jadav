@extends('layouts.app')
@section('title', $customer->name)
@section('breadcrumb')
  <a href="{{ route('customers.index') }}" style="color:#8b5cf6;text-decoration:none">Customers</a> /
  {{ $customer->name }}
@endsection

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">{{ $customer->name }}</h1>
    <p class="page-subtitle">
      @if($customer->customer_code)<span style="color:#8b5cf6;font-weight:600">{{ $customer->customer_code }}</span> &mdash; @endif
      {{ $customer->organization ?? 'Individual Client' }}
    </p>
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

    {{-- Finance summary --}}
    <div class="card">
      <div class="card-body">
        <div style="display:flex;justify-content:space-between;margin-bottom:8px">
          <span style="font-size:0.8rem;color:#6b7280">Total Invoiced</span>
          <span style="font-weight:700;color:#1e1b4b">{{ $activeCompany->currency_symbol }}{{ number_format($customer->invoices->sum('total'), 0) }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:8px">
          <span style="font-size:0.8rem;color:#6b7280">Paid</span>
          <span style="font-weight:700;color:#10b981">{{ $activeCompany->currency_symbol }}{{ number_format($customer->invoices->sum('paid_amount'), 0) }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding-top:8px;border-top:1px solid #f3f4f6">
          <span style="font-size:0.85rem;font-weight:700;color:#1e1b4b">Outstanding</span>
          <span style="font-weight:800;color:{{ $customer->getTotalOutstandingAttribute() > 0 ? '#ef4444' : '#10b981' }};font-size:1rem">
            {{ $activeCompany->currency_symbol }}{{ number_format($customer->getTotalOutstandingAttribute(), 0) }}
          </span>
        </div>
      </div>
    </div>
  </div>

  {{-- Projects & Invoices --}}
  <div>
    <div class="card mb-4">
      <div class="card-header">
        <span style="font-weight:700">Projects ({{ $customer->projects->count() }})</span>
        <a href="{{ route('projects.create') }}?customer_id={{ $customer->id }}" class="btn btn-secondary btn-sm">+ Project</a>
      </div>
      <div class="table-wrapper">
        <table class="table">
          <thead><tr><th>Name</th><th>Status</th><th>End Date</th></tr></thead>
          <tbody>
            @forelse($customer->projects as $proj)
            <tr>
              <td><a href="{{ route('projects.show', $proj) }}" style="font-weight:600;color:#4f46e5;text-decoration:none">{{ $proj->name }}</a></td>
              <td><span class="badge badge-{{ $proj->status }}">{{ ucfirst(str_replace('_',' ',$proj->status)) }}</span></td>
              <td>{{ $proj->end_date ? $proj->end_date->format('d M Y') : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:20px">No projects yet</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <span style="font-weight:700">Invoices ({{ $customer->invoices->count() }})</span>
        <a href="{{ route('invoices.create') }}?customer_id={{ $customer->id }}" class="btn btn-secondary btn-sm">+ Invoice</a>
      </div>
      <div class="table-wrapper">
        <table class="table">
          <thead><tr><th>Invoice #</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($customer->invoices as $inv)
            <tr>
              <td><a href="{{ route('invoices.show', $inv) }}" style="font-weight:600;color:#4f46e5;text-decoration:none">{{ $inv->invoice_number }}</a></td>
              <td>{{ $inv->invoice_date->format('d M Y') }}</td>
              <td>{{ $activeCompany->currency_symbol }}{{ number_format($inv->total, 0) }}</td>
              <td><span class="badge badge-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:20px">No invoices yet</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
