@extends('layouts.app')
@section('title', 'Quotations')
@section('breadcrumb', 'Quotations')

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Quotations</h1>
    <p class="page-subtitle">{{ $quotations->total() }} total quotations</p>
  </div>
  <a href="{{ route('quotations.create') }}" class="btn btn-primary">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Quotation
  </a>
</div>

{{-- Status tabs --}}
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px">
  @foreach(['','draft','sent','accepted','rejected','expired','converted'] as $s)
  <a href="{{ route('quotations.index', array_merge(request()->query(), ['status' => $s])) }}"
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
        <tr><th>Quotation #</th><th>Customer</th><th>Date</th><th>Valid Until</th><th>Total</th><th>Status</th><th style="text-align:right">Actions</th></tr>
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
              @if(in_array($q->status, ['draft','sent']))
                <a href="{{ route('quotations.edit', $q) }}" class="btn btn-secondary btn-xs">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
                </a>
              @endif
              <a href="{{ route('quotations.pdf', $q) }}" target="_blank" class="btn btn-secondary btn-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> PDF
              </a>
              @if(in_array($q->status, ['accepted']))
                <form method="POST" action="{{ route('quotations.convert', $q) }}">
                  @csrf
                  <button type="submit" class="btn btn-success btn-xs">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> Project
                  </button>
                </form>
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
