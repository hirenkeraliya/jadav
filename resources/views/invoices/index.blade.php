@extends('layouts.app')
@section('title', 'Invoices')
@section('breadcrumb', 'Invoices')

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Invoices</h1>
    <p class="page-subtitle">{{ $invoices->total() }} total invoices</p>
  </div>
  <a href="{{ route('invoices.create') }}" class="btn btn-primary">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Invoice
  </a>
</div>

<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px">
  @foreach(['','draft','sent','paid','partial','overdue','cancelled'] as $s)
  <a href="{{ route('invoices.index', array_merge(request()->query(), ['status' => $s])) }}"
     style="padding:6px 14px;border-radius:20px;font-size:0.8rem;font-weight:600;text-decoration:none;
            {{ (request('status') === $s || (!request('status') && $s === '')) ? 'background:#6366f1;color:#fff' : 'background:#fff;color:#6b7280;border:1px solid #e5e7eb' }}">
    {{ $s === '' ? 'All' : ucfirst($s) }}
  </a>
  @endforeach
</div>

<div class="card">
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr><th>Invoice #</th><th>Customer</th><th>Project</th><th>Date</th><th>Total</th><th>Paid</th><th>Balance</th><th>Status</th><th style="text-align:right">Actions</th></tr>
      </thead>
      <tbody>
        @forelse($invoices as $inv)
        <tr>
          <td>
            <a href="{{ route('invoices.show', $inv) }}" style="font-weight:600;color:#4f46e5;text-decoration:none">{{ $inv->invoice_number }}</a>
          </td>
          <td>{{ $inv->customer->name ?? '—' }}</td>
          <td>{{ $inv->project->name ?? '—' }}</td>
          <td>{{ $inv->invoice_date->format('d M Y') }}</td>
          <td style="font-weight:700">{{ $activeCompany->currency_symbol }}{{ number_format($inv->total, 0) }}</td>
          <td style="color:#10b981;font-weight:600">{{ $activeCompany->currency_symbol }}{{ number_format($inv->paid_amount, 0) }}</td>
          <td style="color:{{ $inv->balance_due > 0 ? '#ef4444' : '#10b981' }};font-weight:700">
            {{ $activeCompany->currency_symbol }}{{ number_format($inv->balance_due, 0) }}
          </td>
          <td><span class="badge badge-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span></td>
          <td>
            <div style="display:flex;gap:5px;justify-content:flex-end">
              <a href="{{ route('invoices.show', $inv) }}" class="btn btn-secondary btn-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> View
              </a>
              <a href="{{ route('invoices.pdf', $inv) }}" target="_blank" class="btn btn-secondary btn-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> PDF
              </a>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;padding:40px;color:#9ca3af">No invoices yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($invoices->hasPages())
  <div style="padding:16px 20px;border-top:1px solid #f3f4f6">{{ $invoices->links() }}</div>
  @endif
</div>
@endsection
