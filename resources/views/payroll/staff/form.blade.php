@extends('layouts.app')
@section('title', isset($staff) ? 'Edit Staff' : 'Add Staff')
@section('breadcrumb')
  <a href="{{ route('payroll.staff.index') }}" style="color:#8b5cf6;text-decoration:none">Staff</a> /
  {{ isset($staff) ? 'Edit' : 'New' }}
@endsection

@section('content')
<div style="max-width:700px">
  <h1 class="page-title" style="margin-bottom:24px">{{ isset($staff) ? 'Edit Staff Member' : 'Add Staff Member' }}</h1>

  <div class="card">
    <div class="card-body">
      <form method="POST"
            action="{{ isset($staff) ? route('payroll.staff.update', $staff) : route('payroll.staff.store') }}"
            data-autosave
            data-autosave-key="staff::{{ $staff->id ?? 'new' }}">
        @csrf
        @if(isset($staff)) @method('PUT') @endif

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Full Name <span style="color:#ef4444">*</span></label>
            <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'error' : '' }}"
                   value="{{ old('name', $staff->name ?? '') }}" required>
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
          </div>
          <div>
            <label class="form-label">Designation</label>
            <input type="text" name="designation" class="form-control"
                   placeholder="e.g. Designer, Developer"
                   value="{{ old('designation', $staff->designation ?? '') }}">
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'error' : '' }}"
                   value="{{ old('email', $staff->email ?? '') }}">
            @error('email') <span class="form-error">{{ $message }}</span> @enderror
          </div>
          <div>
            <label class="form-label">Mobile</label>
            <input type="text" name="mobile" class="form-control"
                   value="{{ old('mobile', $staff->mobile ?? '') }}">
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Hourly Rate <span style="color:#ef4444">*</span></label>
            <input type="number" name="hourly_rate" class="form-control {{ $errors->has('hourly_rate') ? 'error' : '' }}"
                   step="0.01" min="0" placeholder="0.00"
                   value="{{ old('hourly_rate', $staff->hourly_rate ?? '') }}" required>
            @error('hourly_rate') <span class="form-error">{{ $message }}</span> @enderror
          </div>
          @if(isset($staff))
          <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="active"   {{ old('status', $staff->status) == 'active'   ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ old('status', $staff->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
          @endif
        </div>

        <div style="margin-bottom:24px">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="3">{{ old('notes', $staff->notes ?? '') }}</textarea>
        </div>

        <div style="display:flex;gap:10px">
          <button type="submit" class="btn btn-primary">
            {{ isset($staff) ? 'Update Staff Member' : 'Add Staff Member' }}
          </button>
          <a href="{{ route('payroll.staff.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
