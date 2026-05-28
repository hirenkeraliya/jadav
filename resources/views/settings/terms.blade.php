@extends('layouts.app')
@section('title', 'Terms Templates')
@section('breadcrumb', 'Settings / Terms Templates')

@push('styles')
@include('partials.settings-list-styles')
<style>
  .terms-card {
    border-radius: 14px;
    border: 1.5px solid var(--color-primary-border);
    background: #fff;
    box-shadow: 0 2px 8px color-mix(in srgb, var(--color-primary) 5%, transparent);
    overflow: hidden;
    margin-bottom: 12px;
    transition: box-shadow 0.2s;
  }
  .terms-card:hover { box-shadow: 0 4px 18px color-mix(in srgb, var(--color-primary) 10%, transparent); }
  .terms-card-header {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 18px;
    cursor: pointer;
    user-select: none;
  }
  .terms-card-icon {
    width: 34px; height: 34px; border-radius: 10px;
    background: var(--color-primary-subtle);
    display: flex; align-items: center; justify-content: center;
    color: var(--color-primary); flex-shrink: 0;
  }
  .terms-card-title { font-size: 0.9rem; font-weight: 700; color: #111827; flex: 1; }
  .terms-badge-group { display: flex; gap: 5px; flex-wrap: wrap; }
  .terms-badge {
    font-size: 0.68rem; font-weight: 700;
    padding: 2px 8px; border-radius: 6px;
  }
  .terms-badge.quote   { background: var(--color-primary-subtle); color: var(--color-primary); border: 1px solid var(--color-primary-border); }
  .terms-badge.invoice { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
  .terms-expand-btn {
    background: none; border: none; cursor: pointer;
    color: #9ca3af; transition: color 0.15s;
    display: flex; align-items: center; gap: 6px;
    font-size: 0.78rem; font-weight: 600;
  }
  .terms-expand-btn:hover { color: var(--color-primary); }
  .terms-expand-btn svg { transition: transform 0.2s; }
  .terms-expand-btn.open svg { transform: rotate(180deg); }
  .terms-card-body {
    border-top: 1px solid #f3f4f6;
    padding: 18px;
    display: none;
    background: #fafaf9;
  }
  .terms-card-body.open { display: block; }
  .terms-content-preview {
    font-size: 0.8rem; color: #6b7280; line-height: 1.6;
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 10px; padding: 10px 14px;
    margin-bottom: 12px;
    max-height: 80px; overflow: hidden;
    position: relative;
  }
  .terms-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
  .terms-form-checks { display: flex; gap: 20px; margin-bottom: 14px; }
  .terms-check-label {
    display: flex; align-items: center; gap: 7px;
    font-size: 0.82rem; font-weight: 600; cursor: pointer;
    color: #374151;
  }
  .add-form-card {
    border: 1.5px dashed var(--color-primary-border);
    border-radius: 14px; padding: 20px;
    background: color-mix(in srgb, var(--color-primary) 3%, #fff);
    margin-bottom: 20px;
  }
  .add-form-title {
    font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.1em; color: var(--color-primary);
    margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
  }
  .add-form-title::after { content: ''; flex: 1; height: 1px; background: var(--color-primary-border); }
  .terms-delete-btn {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 0.78rem; font-weight: 600; padding: 5px 12px;
    border-radius: 8px; border: 1.5px solid #fca5a5;
    background: #fff; color: #dc2626; cursor: pointer;
    transition: all 0.15s;
  }
  .terms-delete-btn:hover { background: #fef2f2; }
</style>
@endpush

@section('content')
<div class="settings-page-wrap">
  @include('partials.settings-nav')

  <div class="settings-main">

    <div class="settings-section-header">
      <div class="settings-section-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      </div>
      <div>
        <h1 class="settings-section-title">Terms Templates</h1>
        <p class="settings-section-sub">Reusable terms &amp; conditions blocks for quotations and invoices</p>
      </div>
    </div>

    @include('partials.settings-flash')

    {{-- Add form --}}
    <div class="add-form-card">
      <div class="add-form-title">Add New Template</div>
      <form method="POST" action="{{ route('settings.terms.store') }}">
        @csrf
        <div class="terms-form-grid">
          <div>
            <label class="form-label">Template Name *</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Standard Terms, Short Form">
          </div>
          <div class="terms-form-checks" style="align-items:flex-end;padding-bottom:2px">
            <label class="terms-check-label">
              <input type="checkbox" name="is_default_quotation" value="1"
                     style="accent-color:var(--color-primary);width:15px;height:15px">
              Default for Quotations
            </label>
            <label class="terms-check-label">
              <input type="checkbox" name="is_default_invoice" value="1"
                     style="accent-color:var(--color-primary);width:15px;height:15px">
              Default for Invoices
            </label>
          </div>
        </div>
        <div style="margin-bottom:16px">
          <label class="form-label">Content *</label>
          <textarea name="content" class="form-control" rows="4" required
                    placeholder="Write your terms and conditions here..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Template</button>
      </form>
    </div>

    {{-- Terms list --}}
    @forelse($terms as $term)
    <div class="terms-card">
      <div class="terms-card-header" onclick="toggleTerms('term{{ $term->id }}')">
        <div class="terms-card-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <span class="terms-card-title">{{ $term->name }}</span>
        <div class="terms-badge-group">
          @if($term->is_default_quotation)
            <span class="terms-badge quote">Quotes</span>
          @endif
          @if($term->is_default_invoice)
            <span class="terms-badge invoice">Invoices</span>
          @endif
        </div>
        <button type="button" class="terms-expand-btn" id="btn-term{{ $term->id }}">
          Edit
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
      </div>

      <div class="terms-card-body" id="term{{ $term->id }}">
        <div class="terms-content-preview">{{ $term->content }}</div>
        <form method="POST" action="{{ route('settings.terms.update', $term) }}">
          @csrf @method('PUT')
          <div class="terms-form-grid">
            <div>
              <label class="form-label">Name *</label>
              <input type="text" name="name" class="form-control" value="{{ $term->name }}" required>
            </div>
            <div class="terms-form-checks" style="align-items:flex-end;padding-bottom:2px">
              <label class="terms-check-label">
                <input type="checkbox" name="is_default_quotation" value="1"
                       style="accent-color:var(--color-primary);width:15px;height:15px"
                       {{ $term->is_default_quotation ? 'checked' : '' }}>
                Default Quotes
              </label>
              <label class="terms-check-label">
                <input type="checkbox" name="is_default_invoice" value="1"
                       style="accent-color:var(--color-primary);width:15px;height:15px"
                       {{ $term->is_default_invoice ? 'checked' : '' }}>
                Default Invoices
              </label>
            </div>
          </div>
          <div style="margin-bottom:14px">
            <label class="form-label">Content *</label>
            <textarea name="content" class="form-control" rows="4" required>{{ $term->content }}</textarea>
          </div>
          <div style="display:flex;gap:8px;align-items:center">
            <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
            <button type="button" onclick="toggleTerms('term{{ $term->id }}')" class="btn btn-secondary btn-sm">Cancel</button>
            <div style="margin-left:auto">
              <form method="POST" action="{{ route('settings.terms.destroy', $term) }}" style="display:inline"
                    onsubmit="return confirm('Delete &quot;{{ addslashes($term->name) }}&quot;?')">
                @csrf @method('DELETE')
                <button type="submit" class="terms-delete-btn">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                  Delete
                </button>
              </form>
            </div>
          </div>
        </form>
      </div>
    </div>
    @empty
    <div class="card">
      <div class="lookup-empty">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        <p>No templates yet. Add one above.</p>
      </div>
    </div>
    @endforelse

  </div>
</div>
@endsection

@push('scripts')
<script>
function toggleTerms(id) {
  const body = document.getElementById(id);
  const btn  = document.getElementById('btn-' + id);
  const open = body.classList.toggle('open');
  btn.classList.toggle('open', open);
  btn.querySelector('span') && (btn.querySelector('span').textContent = open ? 'Close' : 'Edit');
  // update text
  const textNodes = btn.childNodes;
  for (const n of textNodes) {
    if (n.nodeType === 3) { n.textContent = open ? 'Close' : 'Edit'; break; }
  }
}
</script>
@endpush
