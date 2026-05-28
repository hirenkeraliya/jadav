@extends('layouts.app')
@section('title', isset($customer) ? 'Edit Customer' : 'New Customer')
@section('breadcrumb')
  <a href="{{ route('customers.index') }}" style="color:#8b5cf6;text-decoration:none">Customers</a> /
  {{ isset($customer) ? 'Edit' : 'New' }}
@endsection

@section('content')
<div style="max-width:700px">
  <h1 class="page-title" style="margin-bottom:24px">{{ isset($customer) ? 'Edit Customer' : 'Add Customer' }}</h1>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ isset($customer) ? route('customers.update', $customer) : route('customers.store') }}"
            data-autosave
            data-autosave-key="customer::{{ $customer->id ?? 'new' }}">
        @csrf
        @if(isset($customer)) @method('PUT') @endif

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Customer Code <span style="color:#ef4444">*</span></label>
            <input type="text" name="customer_code" class="form-control {{ $errors->has('customer_code') ? 'error' : '' }}"
                   value="{{ old('customer_code', $customer->customer_code ?? '') }}" placeholder="e.g. CUST-001" required>
            @error('customer_code') <span class="form-error">{{ $message }}</span> @enderror
          </div>
          <div>
            <label class="form-label">Full Name <span style="color:#ef4444">*</span></label>
            <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'error' : '' }}"
                   value="{{ old('name', $customer->name ?? '') }}" required>
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Organization</label>
            <input type="text" name="organization" class="form-control"
                   value="{{ old('organization', $customer->organization ?? '') }}">
          </div>
          <div>
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'error' : '' }}"
                   value="{{ old('email', $customer->email ?? '') }}">
            @error('email') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Mobile <span style="color:#ef4444">*</span></label>
            <input type="text" name="mobile" class="form-control {{ $errors->has('mobile') ? 'error' : '' }}"
                   value="{{ old('mobile', $customer->mobile ?? '') }}" required>
            @error('mobile') <span class="form-error">{{ $message }}</span> @enderror
          </div>
          <div>
            <label class="form-label">Source</label>
            <input type="text" name="source" class="form-control" placeholder="Referral, Social Media..."
                   value="{{ old('source', $customer->source ?? '') }}">
          </div>
        </div>

        <div style="margin-bottom:16px">
          <label class="form-label">Address</label>
          <textarea name="address" class="form-control" rows="2">{{ old('address', $customer->address ?? '') }}</textarea>
        </div>

        @if(isset($customer))
        <div style="margin-bottom:16px">
          <label class="form-label">Status</label>
          <select name="status" class="form-control" style="max-width:200px">
            <option value="active"   {{ old('status', $customer->status) == 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $customer->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
        @endif

        <div style="margin-bottom:24px">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="3">{{ old('notes', $customer->notes ?? '') }}</textarea>
        </div>

        <div style="display:flex;gap:10px">
          <button type="submit" class="btn btn-primary">
            {{ isset($customer) ? 'Update Customer' : 'Create Customer' }}
          </button>
          <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
