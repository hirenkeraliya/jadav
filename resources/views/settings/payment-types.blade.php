@extends('layouts.app')
@section('title', 'Payment Types')
@section('breadcrumb', 'Settings / Payment Types')

@push('styles')
@include('partials.settings-list-styles')
@endpush

@section('content')
<div class="settings-page-wrap">
  @include('partials.settings-nav')

  <div class="settings-main">

    <div class="settings-section-header">
      <div class="settings-section-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
      </div>
      <div>
        <h1 class="settings-section-title">Payment Types</h1>
        <p class="settings-section-sub">Define the payment methods accepted by your company</p>
      </div>
    </div>

    @include('partials.settings-flash')

    {{-- Add form --}}
    <div class="card" style="margin-bottom:20px">
      <div class="lookup-add-header">Add New Payment Type</div>
      <div class="lookup-add-body">
        <form method="POST" action="{{ route('settings.payment-types.store') }}" class="lookup-add-form">
          @csrf
          <div class="lookup-add-field">
            <label class="form-label">Name *</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Bank Transfer, UPI, Cash">
          </div>
          <div class="lookup-add-action">
            <button type="submit" class="btn btn-primary">Add Type</button>
          </div>
        </form>
      </div>
    </div>

    {{-- List --}}
    <div class="card">
      @forelse($types as $type)
      <div class="lookup-item">
        <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0">
          <span class="lookup-item-icon">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          </span>
          <span class="lookup-item-name">{{ $type->name }}</span>
        </div>
        <span class="lookup-status-pill {{ $type->is_active ? 'active' : 'inactive' }}">
          <span class="dot"></span>{{ $type->is_active ? 'Active' : 'Inactive' }}
        </span>
        <div class="lookup-actions">
          <form method="POST" action="{{ route('settings.payment-types.update', $type) }}">
            @csrf @method('PUT')
            <input type="hidden" name="name" value="{{ $type->name }}">
            <input type="hidden" name="is_active" value="{{ $type->is_active ? 0 : 1 }}">
            <button type="submit" class="btn-icon-sm {{ $type->is_active ? 'warn' : 'success' }}" title="{{ $type->is_active ? 'Deactivate' : 'Activate' }}">
              @if($type->is_active)
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
              @else
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              @endif
            </button>
          </form>
          <form method="POST" action="{{ route('settings.payment-types.destroy', $type) }}" onsubmit="return confirm('Delete &quot;{{ addslashes($type->name) }}&quot;?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-icon-sm danger" title="Delete">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            </button>
          </form>
        </div>
      </div>
      @empty
      <div class="lookup-empty">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        <p>No payment types yet. Add one above.</p>
      </div>
      @endforelse
    </div>

  </div>
</div>
@endsection
