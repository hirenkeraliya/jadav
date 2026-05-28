@extends('layouts.app')
@section('title', 'Finance Entry Types')
@section('breadcrumb', 'Settings / Finance Types')

@push('styles')
@include('partials.settings-list-styles')
@endpush

@section('content')
<div class="settings-page-wrap">
  @include('partials.settings-nav')

  <div class="settings-main">

    <div class="settings-section-header">
      <div class="settings-section-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div>
        <h1 class="settings-section-title">Finance Entry Types</h1>
        <p class="settings-section-sub">Define income and expense categories for finance entries</p>
      </div>
    </div>

    @include('partials.settings-flash')

    {{-- Add form --}}
    <div class="card" style="margin-bottom:20px">
      <div class="lookup-add-header">Add Entry Type</div>
      <div class="lookup-add-body">
        <form method="POST" action="{{ route('settings.finance-entry-types.store') }}" class="lookup-add-form">
          @csrf
          <div class="lookup-add-field">
            <label class="form-label">Name *</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Material Cost, Consultation Fee">
          </div>
          <div style="min-width:180px">
            <label class="form-label">Direction *</label>
            <select name="direction" class="form-control" required>
              <option value="">— Select —</option>
              <option value="credit">Credit (Income)</option>
              <option value="debit">Debit (Expense)</option>
            </select>
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
          <span class="direction-pill {{ $type->direction }}">
            @if($type->direction === 'credit')
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
            @else
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
            @endif
            {{ ucfirst($type->direction) }}
          </span>
          <span class="lookup-item-name">{{ $type->name }}</span>
        </div>
        <span class="lookup-status-pill {{ $type->is_active ? 'active' : 'inactive' }}">
          <span class="dot"></span>{{ $type->is_active ? 'Active' : 'Inactive' }}
        </span>
        <div class="lookup-actions">
          <form method="POST" action="{{ route('settings.finance-entry-types.update', $type) }}">
            @csrf @method('PUT')
            <input type="hidden" name="name" value="{{ $type->name }}">
            <input type="hidden" name="direction" value="{{ $type->direction }}">
            <input type="hidden" name="is_active" value="{{ $type->is_active ? 0 : 1 }}">
            <button type="submit" class="btn-icon-sm {{ $type->is_active ? 'warn' : 'success' }}" title="{{ $type->is_active ? 'Deactivate' : 'Activate' }}">
              @if($type->is_active)
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
              @else
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              @endif
            </button>
          </form>
          <form method="POST" action="{{ route('settings.finance-entry-types.destroy', $type) }}" onsubmit="return confirm('Delete &quot;{{ addslashes($type->name) }}&quot;?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-icon-sm danger" title="Delete">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            </button>
          </form>
        </div>
      </div>
      @empty
      <div class="lookup-empty">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <p>No finance entry types yet. Add one above.</p>
      </div>
      @endforelse
    </div>

  </div>
</div>
@endsection
