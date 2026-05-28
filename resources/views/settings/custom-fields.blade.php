@extends('layouts.app')
@section('title', 'Custom Fields')
@section('breadcrumb', 'Settings / Custom Fields')

@push('styles')
@include('partials.settings-list-styles')
<style>
  .cf-type-badge {
    display: inline-flex; align-items: center;
    font-size: 0.68rem; font-weight: 700; font-family: 'Courier New', monospace;
    padding: 2px 8px; border-radius: 6px;
    background: #f3f4f6; color: #374151;
    border: 1px solid #e5e7eb;
  }
  .cf-module-badge {
    display: inline-flex; align-items: center;
    font-size: 0.68rem; font-weight: 700; text-transform: capitalize;
    padding: 2px 8px; border-radius: 6px;
    background: var(--color-primary-subtle); color: var(--color-primary);
    border: 1px solid var(--color-primary-border);
  }
  .cf-required-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #ef4444;
    display: inline-block;
    box-shadow: 0 0 4px rgba(239,68,68,0.5);
  }
  .lookup-item-meta {
    display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-top: 3px;
  }
</style>
@endpush

@section('content')
<div class="settings-page-wrap">
  @include('partials.settings-nav')

  <div class="settings-main">

    <div class="settings-section-header">
      <div class="settings-section-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
      </div>
      <div>
        <h1 class="settings-section-title">Custom Fields</h1>
        <p class="settings-section-sub">Extend projects and customers with additional data fields</p>
      </div>
    </div>

    @include('partials.settings-flash')

    {{-- Add form --}}
    <div class="card" style="margin-bottom:20px">
      <div class="lookup-add-header" style="padding-bottom:4px">Add Custom Field</div>
      <div class="lookup-add-body">
        <form method="POST" action="{{ route('settings.custom-fields.store') }}">
          @csrf
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px">
            <div>
              <label class="form-label">Label *</label>
              <input type="text" name="label" class="form-control" required placeholder="e.g. Site Area">
            </div>
            <div>
              <label class="form-label">Module *</label>
              <select name="module" class="form-control" required>
                <option value="">&mdash; Select &mdash;</option>
                <option value="project">Project</option>
                <option value="customer"Client</option>
              </select>
            </div>
            <div>
              <label class="form-label">Field Type *</label>
              <select name="type" id="cfType" class="form-control" required onchange="toggleOptions(this.value)">
                <option value="text">Text</option>
                <option value="number">Number</option>
                <option value="date">Date</option>
                <option value="textarea">Textarea</option>
                <option value="select">Select (dropdown)</option>
                <option value="radio">Radio</option>
                <option value="checkbox">Checkbox</option>
              </select>
            </div>
          </div>
          <div id="optionsRow" style="display:none;margin-bottom:14px">
            <label class="form-label">Options <span style="color:#9ca3af;font-weight:400">(one per line)</span></label>
            <textarea name="options" class="form-control" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
          </div>
          <div style="display:flex;align-items:center;gap:20px;margin-bottom:16px">
            <label style="display:flex;align-items:center;gap:7px;font-size:0.83rem;font-weight:600;cursor:pointer;color:#374151">
              <input type="checkbox" name="is_required" value="1"
                     style="accent-color:var(--color-primary);width:14px;height:14px">
              Required field
            </label>
            <div style="display:flex;align-items:center;gap:8px">
              <label class="form-label" style="margin:0;white-space:nowrap">Sort Order</label>
              <input type="number" name="sort_order" class="form-control" style="width:80px" value="0" min="0">
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Add Field</button>
        </form>
      </div>
    </div>

    {{-- Fields list --}}
    <div class="card">
      @forelse($fields as $field)
      <div class="lookup-item">
        <div style="flex:1;min-width:0">
          <div style="display:flex;align-items:center;gap:8px">
            <span class="lookup-item-name">{{ $field->label }}</span>
            @if($field->is_required)
              <span class="cf-required-dot" title="Required"></span>
            @endif
          </div>
          <div class="lookup-item-meta">
            <span class="cf-module-badge">{{ $field->module }}</span>
            <span class="cf-type-badge">{{ $field->type }}</span>
            <span style="font-size:0.72rem;color:#d1d5db">&middot;</span>
            <span style="font-size:0.72rem;color:#9ca3af">order: {{ $field->sort_order }}</span>
          </div>
        </div>
        <span class="lookup-status-pill {{ $field->is_active ? 'active' : 'inactive' }}">
          <span class="dot"></span>{{ $field->is_active ? 'Active' : 'Inactive' }}
        </span>
        <div class="lookup-actions">
          <form method="POST" action="{{ route('settings.custom-fields.update', $field) }}">
            @csrf @method('PUT')
            <input type="hidden" name="label" value="{{ $field->label }}">
            <input type="hidden" name="module" value="{{ $field->module }}">
            <input type="hidden" name="type" value="{{ $field->type }}">
            <input type="hidden" name="options" value="{{ $field->options }}">
            <input type="hidden" name="is_required" value="{{ $field->is_required ? '1' : '0' }}">
            <input type="hidden" name="sort_order" value="{{ $field->sort_order }}">
            <input type="hidden" name="is_active" value="{{ $field->is_active ? 0 : 1 }}">
            <button type="submit" class="btn-icon-sm {{ $field->is_active ? 'warn' : 'success' }}"
                    title="{{ $field->is_active ? 'Disable' : 'Enable' }}">
              @if($field->is_active)
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
              @else
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              @endif
            </button>
          </form>
          <form method="POST" action="{{ route('settings.custom-fields.destroy', $field) }}"
                onsubmit="return confirm('Delete &quot;{{ addslashes($field->label) }}&quot;?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-icon-sm danger" title="Delete">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            </button>
          </form>
        </div>
      </div>
      @empty
      <div class="lookup-empty">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
        <p>No custom fields yet. Add one above.</p>
      </div>
      @endforelse
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
function toggleOptions(type) {
  document.getElementById('optionsRow').style.display = ['select','radio','checkbox'].includes(type) ? 'block' : 'none';
}
</script>
@endpush
