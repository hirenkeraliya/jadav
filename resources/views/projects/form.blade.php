@extends('layouts.app')
@section('title', isset($project) ? 'Edit Project' : 'New Project')
@section('breadcrumb')
  <a href="{{ route('projects.index') }}" style="color:#8b5cf6;text-decoration:none">Projects</a> /
  {{ isset($project) ? 'Edit' : 'New' }}
@endsection

@section('content')
<div style="max-width:900px">
  <h1 class="page-title" style="margin-bottom:24px">{{ isset($project) ? 'Edit Project' : 'New Project' }}</h1>

  <div class="card">
    <div class="card-body">
      <form method="POST"
            action="{{ isset($project) ? route('projects.update', $project) : route('projects.store') }}"
            enctype="multipart/form-data">
        @csrf
        @if(isset($project)) @method('PUT') @endif

        {{-- Section: Basic Info --}}
        <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#8b5cf6;margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid #ede9fe">
          Basic Information
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Project Code <span style="color:#ef4444">*</span></label>
            <input type="text" name="project_code" class="form-control {{ $errors->has('project_code') ? 'error' : '' }}"
                   value="{{ old('project_code', $project->project_code ?? '') }}" required placeholder="e.g. PRJ-0001">
            @error('project_code') <span class="form-error">{{ $message }}</span> @enderror
          </div>
          <div>
            <label class="form-label">Project Name <span style="color:#ef4444">*</span></label>
            <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'error' : '' }}"
                   value="{{ old('name', $project->name ?? '') }}" required>
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div style="margin-bottom:16px">
            <label class="form-label">Customer <span style="color:#ef4444">*</span></label>
            <select name="customer_id" class="form-control {{ $errors->has('customer_id') ? 'error' : '' }}" required>
              <option value="">— Select Customer —</option>
              @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ old('customer_id', $project->customer_id ?? request('customer_id')) == $c->id ? 'selected' : '' }}>
                  {{ $c->name }}{{ $c->organization ? ' ('.$c->organization.')' : '' }}
                </option>
              @endforeach
            </select>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Project Type</label>
            <select name="project_type_id" class="form-control">
              <option value="">— None —</option>
              @foreach($projectTypes as $pt)
                <option value="{{ $pt->id }}" {{ old('project_type_id', $project->project_type_id ?? '') == $pt->id ? 'selected' : '' }}>
                  {{ $pt->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="form-label">Status <span style="color:#ef4444">*</span></label>
            <select name="status" class="form-control" required>
              @foreach(['pending','running','on_hold','delayed','completed','invoiced','cancelled'] as $s)
                <option value="{{ $s }}" {{ old('status', $project->status ?? 'pending') == $s ? 'selected' : '' }}>
                  {{ ucfirst(str_replace('_',' ',$s)) }}
                </option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="form-label">Priority</label>
            <select name="priority" class="form-control">
              @foreach(['low','medium','high'] as $p)
                <option value="{{ $p }}" {{ old('priority', $project->priority ?? 'medium') == $p ? 'selected' : '' }}>
                  {{ ucfirst($p) }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control"
                   value="{{ old('start_date', isset($project) ? $project->start_date?->format('Y-m-d') : '') }}">
          </div>
          <div>
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control"
                   value="{{ old('end_date', isset($project) ? $project->end_date?->format('Y-m-d') : '') }}">
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Location / City</label>
            <input type="text" name="location" class="form-control"
                   value="{{ old('location', $project->location ?? '') }}">
          </div>
          <div>
            <label class="form-label">Estimated Amount</label>
            <input type="number" name="estimated_amount" class="form-control" step="0.01" min="0"
                   value="{{ old('estimated_amount', $project->estimated_amount ?? '') }}">
          </div>
        </div>

        <div style="margin-bottom:16px">
          <label class="form-label">Site Address</label>
          <textarea name="site_address" class="form-control" rows="2">{{ old('site_address', $project->site_address ?? '') }}</textarea>
        </div>

        <div style="margin-bottom:16px">
          <label class="form-label">Lead By</label>
          <select name="lead_by" class="form-control">
            <option value="">— Assign team lead —</option>
            @foreach($users as $u)
              <option value="{{ $u->id }}" {{ old('lead_by', $project->lead_by ?? '') == $u->id ? 'selected' : '' }}>
                {{ $u->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div style="margin-bottom:24px">
          <label class="form-label">Scope of Work</label>
          <textarea name="scope_of_work" class="form-control" rows="4">{{ old('scope_of_work', $project->scope_of_work ?? '') }}</textarea>
        </div>

        <div style="margin-bottom:24px">
          <label class="form-label">Internal Notes</label>
          <textarea name="internal_notes" class="form-control" rows="2">{{ old('internal_notes', $project->internal_notes ?? '') }}</textarea>
        </div>

        {{-- Custom fields --}}
        @if($customFields->isNotEmpty())
        <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#8b5cf6;margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid #ede9fe;margin-top:24px">
          Custom Fields
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
          @foreach($customFields as $field)
          <div>
            <label class="form-label">{{ $field->label }}{{ $field->is_required ? ' *' : '' }}</label>
            @php $val = old('custom_fields.'.$field->field_key, $customFieldValues[$field->field_key] ?? $field->default_value) @endphp
            @if($field->type === 'textarea')
              <textarea name="custom_fields[{{ $field->field_key }}]" class="form-control"
                        placeholder="{{ $field->placeholder }}"
                        {{ $field->is_required ? 'required' : '' }}>{{ $val }}</textarea>
            @elseif(in_array($field->type, ['select','multiselect']))
              <select name="custom_fields[{{ $field->field_key }}]{{ $field->type==='multiselect' ? '[]' : '' }}"
                      class="form-control" {{ $field->is_required ? 'required' : '' }} {{ $field->type==='multiselect' ? 'multiple' : '' }}>
                @if($field->type === 'select') <option value="">— Select —</option> @endif
                @foreach($field->options ?? [] as $opt)
                  <option value="{{ $opt }}" {{ $val == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
              </select>
            @elseif($field->type === 'toggle')
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-top:8px">
                <input type="hidden" name="custom_fields[{{ $field->field_key }}]" value="0">
                <input type="checkbox" name="custom_fields[{{ $field->field_key }}]" value="1"
                       style="accent-color:#6366f1;width:18px;height:18px" {{ $val ? 'checked' : '' }}>
                <span style="font-size:0.875rem;color:#6b7280">{{ $field->placeholder ?? 'Yes' }}</span>
              </label>
            @else
              <input type="{{ $field->type === 'number' ? 'number' : ($field->type === 'date' ? 'date' : ($field->type === 'url' ? 'url' : 'text')) }}"
                     name="custom_fields[{{ $field->field_key }}]"
                     class="form-control"
                     value="{{ $val }}"
                     placeholder="{{ $field->placeholder }}"
                     {{ $field->is_required ? 'required' : '' }}>
            @endif
          </div>
          @endforeach
        </div>
        @endif

        {{-- File attachments (only on create) --}}
        @unless(isset($project))
        <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#8b5cf6;margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid #ede9fe">
          File Attachments
        </div>
        <div style="margin-bottom:24px">
          <label class="form-label">Upload Files</label>
          <input type="file" name="files[]" multiple class="form-control">
          <span class="form-hint">Images, PDFs, Word docs (max 10MB each)</span>
        </div>
        @endunless

        <div style="display:flex;gap:10px">
          <button type="submit" class="btn btn-primary">
            {{ isset($project) ? 'Update Project' : 'Create Project' }}
          </button>
          <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
