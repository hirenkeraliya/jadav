@extends('layouts.app')
@section('title', 'Terms Templates')
@section('breadcrumb', 'Settings / Terms Templates')

@section('content')
<div style="display:flex;gap:20px;max-width:1100px">
  @include('partials.settings-nav')
  <div style="flex:1">
    <h1 class="page-title" style="margin-bottom:24px">Terms & Conditions Templates</h1>

    <div class="card mb-4">
      <div class="card-header"><span style="font-weight:700">Add Template</span></div>
      <div class="card-body">
        <form method="POST" action="{{ route('settings.terms.store') }}">
          @csrf
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:14px">
            <div>
              <label class="form-label">Template Name</label>
              <input type="text" name="name" class="form-control" required placeholder="e.g. Standard Terms">
            </div>
            <div style="display:flex;align-items:flex-end;gap:16px">
              <label style="display:flex;align-items:center;gap:6px;font-size:0.85rem;font-weight:500;cursor:pointer;padding-bottom:2px">
                <input type="checkbox" name="is_default_quotation" value="1"> Default for Quotations
              </label>
              <label style="display:flex;align-items:center;gap:6px;font-size:0.85rem;font-weight:500;cursor:pointer;padding-bottom:2px">
                <input type="checkbox" name="is_default_invoice" value="1"> Default for Invoices
              </label>
            </div>
          </div>
          <div style="margin-bottom:14px">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="5" required placeholder="Write your terms and conditions here..."></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Add Template</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="table-wrapper">
        <table class="table">
          <thead><tr><th>Name</th><th>Default For</th><th style="text-align:right">Actions</th></tr></thead>
          <tbody>
            @forelse($terms as $term)
            <tr>
              <td style="font-weight:600">{{ $term->name }}</td>
              <td>
                <div style="display:flex;gap:4px;flex-wrap:wrap">
                  @if($term->is_default_quotation) <span class="badge badge-active">Quotes</span> @endif
                  @if($term->is_default_invoice) <span class="badge badge-paid">Invoices</span> @endif
                </div>
              </td>
              <td>
                <div style="display:flex;gap:6px;justify-content:flex-end">
                  <button onclick="toggleEdit('t{{ $term->id }}')" class="btn btn-secondary btn-xs">Edit</button>
                  <form method="POST" action="{{ route('settings.terms.destroy', $term) }}" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
            <tr id="t{{ $term->id }}" style="display:none;background:#f9fafb">
              <td colspan="3" style="padding:12px 16px">
                <form method="POST" action="{{ route('settings.terms.update', $term) }}">
                  @csrf @method('PUT')
                  <div style="margin-bottom:12px">
                    <input type="text" name="name" class="form-control" value="{{ $term->name }}" required>
                  </div>
                  <textarea name="content" class="form-control" rows="4" style="margin-bottom:10px" required>{{ $term->content }}</textarea>
                  <div style="display:flex;gap:16px;align-items:center;margin-bottom:10px">
                    <label style="display:flex;align-items:center;gap:6px;font-size:0.85rem;cursor:pointer">
                      <input type="checkbox" name="is_default_quotation" value="1" {{ $term->is_default_quotation ? 'checked' : '' }}> Default for Quotes
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;font-size:0.85rem;cursor:pointer">
                      <input type="checkbox" name="is_default_invoice" value="1" {{ $term->is_default_invoice ? 'checked' : '' }}> Default for Invoices
                    </label>
                  </div>
                  <div style="display:flex;gap:8px">
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    <button type="button" onclick="toggleEdit('t{{ $term->id }}')" class="btn btn-secondary btn-sm">Cancel</button>
                  </div>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:20px">No templates yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function toggleEdit(id) {
  const r = document.getElementById(id);
  r.style.display = r.style.display === 'none' ? 'table-row' : 'none';
}
</script>
@endpush
