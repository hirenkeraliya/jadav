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

        <div style="margin-bottom:16px" x-data="quickCustomer()">
            <label class="form-label">Customer <span style="color:#ef4444">*</span></label>
            <div style="display:flex;gap:8px;align-items:center">
              <select name="customer_id" id="customer_id_select" class="form-control {{ $errors->has('customer_id') ? 'error' : '' }}" required style="flex:1">
                <option value="">— Select Customer —</option>
                @foreach($customers as $c)
                  <option value="{{ $c->id }}" {{ old('customer_id', $project->customer_id ?? request('customer_id')) == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}{{ $c->organization ? ' ('.$c->organization.')' : '' }}
                  </option>
                @endforeach
              </select>
              <button type="button" @click="open = true"
                      style="white-space:nowrap;padding:0 14px;height:38px;background:#8b5cf6;color:#fff;border:none;border-radius:6px;font-size:0.8rem;font-weight:600;cursor:pointer;flex-shrink:0">
                + New Customer
              </button>
            </div>
            @error('customer_id') <span class="form-error">{{ $message }}</span> @enderror

            {{-- Quick-add customer modal --}}
            <template x-teleport="body">
              <div x-show="open" x-cloak
                   style="position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,0.45)"
                   @keydown.escape.window="open = false">
                <div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%">
                <div style="background:#fff;border-radius:12px;padding:28px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,0.2)" @click.stop>
                  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                    <h3 style="margin:0;font-size:1rem;font-weight:700;color:#1e1b4b">Quick Add Customer</h3>
                    <button type="button" @click="open = false" style="background:none;border:none;cursor:pointer;font-size:1.3rem;color:#9ca3af;line-height:1">&times;</button>
                  </div>

                  <div style="margin-bottom:14px">
                    <label class="form-label">Name <span style="color:#ef4444">*</span></label>
                    <input type="text" x-model="form.name" class="form-control" placeholder="Customer name" @keydown.enter.prevent>
                    <span x-show="errors.name" x-text="errors.name" class="form-error"></span>
                  </div>
                  <div style="margin-bottom:14px">
                    <label class="form-label">Organization</label>
                    <input type="text" x-model="form.organization" class="form-control" placeholder="Company / organization" @keydown.enter.prevent>
                  </div>
                  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px">
                    <div>
                      <label class="form-label">Email</label>
                      <input type="email" x-model="form.email" class="form-control" placeholder="email@example.com" @keydown.enter.prevent>
                    </div>
                    <div>
                      <label class="form-label">Mobile</label>
                      <input type="text" x-model="form.mobile" class="form-control" placeholder="+91 …" @keydown.enter.prevent>
                    </div>
                  </div>

                  <div x-show="serverError" x-text="serverError" style="color:#ef4444;font-size:0.8rem;margin-bottom:12px"></div>

                  <div style="display:flex;gap:10px;justify-content:flex-end">
                    <button type="button" @click="open = false"
                            style="padding:8px 16px;border:1px solid #d1d5db;border-radius:6px;background:#fff;cursor:pointer;font-size:0.85rem">
                      Cancel
                    </button>
                    <button type="button" @click="save()" :disabled="saving"
                            style="padding:8px 16px;background:#8b5cf6;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:0.85rem;font-weight:600"
                            :style="saving ? 'opacity:0.7;cursor:not-allowed' : ''">
                      <span x-text="saving ? 'Saving…' : 'Add Customer'"></span>
                    </button>
                  </div>
                </div>
                </div>
              </div>
            </template>
        </div>

        @push('scripts')
        <script>
        function quickCustomer() {
            return {
                open: false,
                saving: false,
                serverError: '',
                form: { name: '', organization: '', email: '', mobile: '' },
                errors: {},
                save() {
                    this.errors = {};
                    this.serverError = '';
                    if (!this.form.name.trim()) {
                        this.errors.name = 'Name is required.';
                        return;
                    }
                    this.saving = true;
                    fetch('{{ route('customers.quick-store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.form),
                    })
                    .then(r => r.json().then(data => ({ ok: r.ok, data })))
                    .then(({ ok, data }) => {
                        if (!ok) {
                            if (data.errors) {
                                this.errors = Object.fromEntries(
                                    Object.entries(data.errors).map(([k, v]) => [k, v[0]])
                                );
                            } else {
                                this.serverError = data.message || 'Something went wrong.';
                            }
                            return;
                        }
                        const sel = document.getElementById('customer_id_select');
                        const opt = new Option(data.label, data.id, true, true);
                        sel.add(opt);
                        sel.value = data.id;
                        this.open = false;
                        this.form = { name: '', organization: '', email: '', mobile: '' };
                    })
                    .catch(() => { this.serverError = 'Network error. Please try again.'; })
                    .finally(() => { this.saving = false; });
                },
            };
        }
        </script>
        @endpush

        @if($projectTypes->isNotEmpty())
        <div style="margin-bottom:16px">
          <label class="form-label">Project Types</label>
          @php $selectedTypeIds = old('project_type_ids', isset($project) ? $project->projectTypes->pluck('id')->toArray() : []); @endphp
          <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:6px">
            @foreach($projectTypes as $pt)
            <label style="display:inline-flex;align-items:center;gap:7px;cursor:pointer;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:6px 12px;font-size:0.875rem;transition:all 0.15s"
                   onclick="this.style.background=this.querySelector('input').checked?'#f9fafb':'#ede9fe';this.style.borderColor=this.querySelector('input').checked?'#e5e7eb':'#8b5cf6'">
              <input type="checkbox" name="project_type_ids[]" value="{{ $pt->id }}"
                     style="accent-color:#8b5cf6;width:15px;height:15px"
                     {{ in_array($pt->id, (array)$selectedTypeIds) ? 'checked' : '' }}>
              <span style="width:10px;height:10px;border-radius:50%;background:{{ $pt->color }};flex-shrink:0"></span>
              {{ $pt->name }}
            </label>
            @endforeach
          </div>
        </div>
        @endif

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
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
