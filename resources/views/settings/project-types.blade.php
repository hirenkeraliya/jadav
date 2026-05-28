@extends('layouts.app')
@section('title', 'Project Types')
@section('breadcrumb', 'Settings / Project Types')

@push('styles')
@include('partials.settings-list-styles')
@endpush

@section('content')
<div class="settings-page-wrap">
  @include('partials.settings-nav')

  <div class="settings-main">

    <div class="settings-section-header">
      <div class="settings-section-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      </div>
      <div>
        <h1 class="settings-section-title">Project Types</h1>
        <p class="settings-section-sub">Categorise projects by type for better organisation</p>
      </div>
    </div>

    @include('partials.settings-flash')

    {{-- Add form --}}
    <div class="card" style="margin-bottom:20px">
      <div class="lookup-add-header">Add New Type</div>
      <div class="lookup-add-body">
        <form method="POST" action="{{ route('settings.project-types.store') }}" class="lookup-add-form">
          @csrf
          <div class="lookup-add-field">
            <label class="form-label">Name *</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Residential Interior">
          </div>
          <div class="lookup-add-color">
            <label class="form-label">Color</label>
            <div style="display:flex;align-items:center;gap:8px">
              <div class="color-swatch-sm">
                <input type="color" name="color" id="ptColor" value="#6366f1" oninput="document.getElementById('ptColorText').value=this.value;document.getElementById('ptPreviewDot').style.background=this.value">
              </div>
              <input type="text" id="ptColorText" class="form-control color-hex-sm" value="#6366f1" maxlength="7"
                     oninput="if(/^#[0-9a-fA-F]{6}$/.test(this.value)){document.getElementById('ptColor').value=this.value;document.getElementById('ptPreviewDot').style.background=this.value}">
              <span class="color-dot" id="ptPreviewDot" style="background:#6366f1"></span>
            </div>
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
          <span class="type-color-swatch" style="background:{{ $type->color }}"></span>
          <span class="lookup-item-name">{{ $type->name }}</span>
        </div>
        <span class="lookup-status-pill {{ $type->is_active ? 'active' : 'inactive' }}">
          <span class="dot"></span>{{ $type->is_active ? 'Active' : 'Inactive' }}
        </span>
        <div class="lookup-actions">
          <form method="POST" action="{{ route('settings.project-types.update', $type) }}">
            @csrf @method('PUT')
            <input type="hidden" name="name" value="{{ $type->name }}">
            <input type="hidden" name="color" value="{{ $type->color }}">
            <input type="hidden" name="is_active" value="{{ $type->is_active ? 0 : 1 }}">
            <button type="submit" class="btn-icon-sm {{ $type->is_active ? 'warn' : 'success' }}" title="{{ $type->is_active ? 'Deactivate' : 'Activate' }}">
              @if($type->is_active)
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
              @else
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              @endif
            </button>
          </form>
          <form method="POST" action="{{ route('settings.project-types.destroy', $type) }}" onsubmit="return confirm('Delete &quot;{{ addslashes($type->name) }}&quot;?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-icon-sm danger" title="Delete">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            </button>
          </form>
        </div>
      </div>
      @empty
      <div class="lookup-empty">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
        <p>No project types yet. Add one above.</p>
      </div>
      @endforelse
    </div>

  </div>
</div>
@endsection
