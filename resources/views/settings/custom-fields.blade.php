@extends('layouts.app')
@section('title', 'Custom Fields')
@section('breadcrumb', 'Settings / Custom Fields')

@section('content')
<div style="display:flex;gap:20px;max-width:1100px">
  @include('partials.settings-nav')
  <div style="flex:1">
    <h1 class="page-title" style="margin-bottom:24px">Custom Fields</h1>

    <div class="card mb-4">
      <div class="card-header"><span style="font-weight:700">Add Custom Field</span></div>
      <div class="card-body">
        <form method="POST" action="{{ route('settings.custom-fields.store') }}">
          @csrf
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px">
            <div>
              <label class="form-label">Label</label>
              <input type="text" name="label" class="form-control" required placeholder="e.g. Site Area">
            </div>
            <div>
              <label class="form-label">Module</label>
              <select name="module" class="form-control" required>
                <option value="">— Select —</option>
                <option value="project">Project</option>
                <option value="customer">Customer</option>
              </select>
            </div>
            <div>
              <label class="form-label">Field Type</label>
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
          <div style="display:flex;align-items:center;gap:16px;margin-bottom:14px">
            <label style="display:flex;align-items:center;gap:6px;font-size:0.85rem;font-weight:500;cursor:pointer">
              <input type="checkbox" name="is_required" value="1"> Required
            </label>
            <div>
              <label class="form-label" style="font-size:0.78rem;margin-bottom:4px">Sort Order</label>
              <input type="number" name="sort_order" class="form-control" style="width:80px" value="0" min="0">
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Add Field</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="table-wrapper">
        <table class="table">
          <thead><tr><th>Label</th><th>Module</th><th>Type</th><th>Required</th><th>Order</th><th>Active</th><th style="text-align:right">Actions</th></tr></thead>
          <tbody>
            @forelse($fields as $field)
            <tr>
              <td style="font-weight:600">{{ $field->label }}</td>
              <td><span class="badge badge-draft" style="text-transform:capitalize">{{ $field->module }}</span></td>
              <td style="font-family:monospace;font-size:0.82rem">{{ $field->type }}</td>
              <td>{{ $field->is_required ? '✓' : '—' }}</td>
              <td>{{ $field->sort_order }}</td>
              <td><span class="badge badge-{{ $field->is_active ? 'active' : 'inactive' }}">{{ $field->is_active ? 'Active' : 'Inactive' }}</span></td>
              <td>
                <div style="display:flex;gap:6px;justify-content:flex-end">
                  <form method="POST" action="{{ route('settings.custom-fields.update', $field) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="label" value="{{ $field->label }}">
                    <input type="hidden" name="module" value="{{ $field->module }}">
                    <input type="hidden" name="type" value="{{ $field->type }}">
                    <input type="hidden" name="options" value="{{ $field->options }}">
                    <input type="hidden" name="is_required" value="{{ $field->is_required ? '1' : '0' }}">
                    <input type="hidden" name="sort_order" value="{{ $field->sort_order }}">
                    <input type="hidden" name="is_active" value="{{ $field->is_active ? 0 : 1 }}">
                    <button type="submit" class="btn btn-secondary btn-xs">{{ $field->is_active ? 'Disable' : 'Enable' }}</button>
                  </form>
                  <form method="POST" action="{{ route('settings.custom-fields.destroy', $field) }}" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:#9ca3af;padding:20px">No custom fields yet.</td></tr>
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
function toggleOptions(type) {
  document.getElementById('optionsRow').style.display = ['select','radio','checkbox'].includes(type) ? 'block' : 'none';
}
</script>
@endpush
