@extends('layouts.app')
@section('title', 'Quotations')
@section('breadcrumb', 'Quotations')

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Quotations</h1>
    <p class="page-subtitle">{{ $quotations->total() }} total quotations</p>
  </div>
  @can('quotations.create')
  <a href="{{ route('quotations.create') }}" class="btn btn-primary">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Quotation
  </a>
  @endcan
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
  <div class="stat-card" style="padding:16px">
    <div style="font-size:0.7rem;font-weight:700;color:#6366f1;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Total Quotations</div>
    <div style="font-size:1.4rem;font-weight:800;color:#1e1b4b">{{ number_format($totalQuotations) }}</div>
  </div>
  <div class="stat-card" style="padding:16px">
    <div style="font-size:0.7rem;font-weight:700;color:#f59e0b;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Pending</div>
    <div style="font-size:1.4rem;font-weight:800;color:#1e1b4b">{{ number_format($pendingQuotations) }}</div>
  </div>
  <div class="stat-card" style="padding:16px">
    <div style="font-size:0.7rem;font-weight:700;color:#10b981;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Total Amount (Sent)</div>
    <div style="font-size:1.4rem;font-weight:800;color:#1e1b4b">{{ $activeCompany->currency_symbol }}{{ number_format($totalAmount, 0) }}</div>
  </div>
  <div class="stat-card" style="padding:16px">
    <div style="font-size:0.7rem;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Conversion Ratio</div>
    <div style="font-size:1.4rem;font-weight:800;color:#1e1b4b">{{ $conversionRatio }}%</div>
  </div>
</div>

{{-- Status tabs --}}
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px">
  @foreach(['' => 'All', 'sent' => 'Sent to Client', 'rejected' => 'Rejected', 'converted' => 'Converted'] as $value => $label)
  <a href="{{ route('quotations.index', array_merge(request()->query(), ['status' => $value])) }}"
     style="padding:6px 14px;border-radius:20px;font-size:0.8rem;font-weight:600;text-decoration:none;
            {{ (request('status') === $value || (!request('status') && $value === '')) ? 'background:#6366f1;color:#fff' : 'background:#fff;color:#6b7280;border:1px solid #e5e7eb' }}">
    {{ $label }}
  </a>
  @endforeach
</div>

<div class="card">
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr><th>Quotation #</th><th>Client</th><th>Date</th><th>Valid Until</th><th>Total</th><th>Status</th><th style="text-align:right">Actions</th></tr>
      </thead>
      <tbody>
        @forelse($quotations as $q)
        <tr>
          <td>
            <a href="{{ route('quotations.show', $q) }}" style="font-weight:600;color:#4f46e5;text-decoration:none">{{ $q->quotation_number }}</a>
            @if($q->version > 1) <span style="font-size:0.72rem;color:#8b5cf6;margin-left:4px">v{{ $q->version }}</span> @endif
          </td>
          <td>{{ $q->customer->name ?? '—' }}</td>
          <td>{{ $q->date->format('d M Y') }}</td>
          <td>{{ $q->valid_until ? $q->valid_until->format('d M Y') : '—' }}</td>
          <td style="font-weight:700">{{ $activeCompany->currency_symbol }}{{ number_format($q->total, 0) }}</td>
          <td><span class="badge badge-{{ $q->status }}">{{ ucfirst($q->status) }}</span></td>
          <td>
            <div style="display:flex;gap:5px;justify-content:flex-end">
              <a href="{{ route('quotations.show', $q) }}" class="btn btn-secondary btn-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> View
              </a>
              @if($q->status === 'sent')
                @can('quotations.edit')
                <a href="{{ route('quotations.edit', $q) }}" class="btn btn-secondary btn-xs">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
                </a>
                @endcan
              @endif
              <a href="{{ route('quotations.pdf', $q) }}" target="_blank" class="btn btn-secondary btn-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> PDF
              </a>
              @if($q->status === 'sent' && !$q->project)
                @can('projects.create')
                <a href="{{ route('quotations.show', $q) }}" class="btn btn-success btn-xs" title="Convert to Project">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> Project
                </a>
                @endcan
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;padding:40px;color:#9ca3af">No quotations yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($quotations->hasPages())
  <div style="padding:16px 20px;border-top:1px solid #f3f4f6">{{ $quotations->links() }}</div>
  @endif
</div>
@endsection
