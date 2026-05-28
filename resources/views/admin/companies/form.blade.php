@extends('layouts.app')
@section('title', isset($company) ? 'Edit Company' : 'New Company')
@section('breadcrumb')
  <a href="{{ route('admin.companies.index') }}" style="color:#8b5cf6;text-decoration:none">Companies</a> / {{ isset($company) ? 'Edit' : 'New' }}
@endsection

@section('content')
<div style="max-width:700px">
  <h1 class="page-title" style="margin-bottom:24px">{{ isset($company) ? 'Edit Company' : 'New Company' }}</h1>

  <div class="card">
    <div class="card-body">
      <form method="POST"
            action="{{ isset($company) ? route('admin.companies.update', $company) : route('admin.companies.store') }}"
            enctype="multipart/form-data"
            data-autosave
            data-autosave-key="company::{{ $company->id ?? 'new' }}">
        @csrf
        @if(isset($company)) @method('PUT') @endif

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Company Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $company->name ?? '') }}" required>
          </div>
          <div>
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $company->email ?? '') }}" required>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone ?? '') }}">
          </div>
          <div>
            <label class="form-label">Website</label>
            <input type="url" name="website" class="form-control" value="{{ old('website', $company->website ?? '') }}" placeholder="https://...">
          </div>
        </div>

        <div style="margin-bottom:16px">
          <label class="form-label">Address</label>
          <textarea name="address" class="form-control" rows="2">{{ old('address', $company->address ?? '') }}</textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Currency</label>
            <input type="text" name="currency" class="form-control" value="{{ old('currency', $company->currency ?? 'INR') }}" required>
          </div>
          <div>
            <label class="form-label">Symbol</label>
            <input type="text" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $company->currency_symbol ?? '₹') }}" required>
          </div>
          <div>
            <label class="form-label">FY Start Month</label>
            <input type="number" name="financial_year_start" class="form-control" min="1" max="12" value="{{ old('financial_year_start', $company->financial_year_start ?? 4) }}" required>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Primary Color</label>
            <div style="display:flex;gap:8px;align-items:center">
              <input type="color" name="primary_color" class="form-control" style="width:48px;height:38px;padding:2px;cursor:pointer" value="{{ old('primary_color', $company->primary_color ?? '#6366f1') }}">
              <span style="font-size:0.82rem;color:#9ca3af">{{ $company->primary_color ?? '#6366f1' }}</span>
            </div>
          </div>
          <div>
            <label class="form-label">Secondary Color</label>
            <div style="display:flex;gap:8px;align-items:center">
              <input type="color" name="secondary_color" class="form-control" style="width:48px;height:38px;padding:2px;cursor:pointer" value="{{ old('secondary_color', $company->secondary_color ?? '#f59e0b') }}">
              <span style="font-size:0.82rem;color:#9ca3af">{{ $company->secondary_color ?? '#f59e0b' }}</span>
            </div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Invoice Prefix</label>
            <input type="text" name="invoice_prefix" class="form-control" value="{{ old('invoice_prefix', $company->invoice_prefix ?? 'INV') }}" required>
          </div>
          <div>
            <label class="form-label">Quotation Prefix</label>
            <input type="text" name="quotation_prefix" class="form-control" value="{{ old('quotation_prefix', $company->quotation_prefix ?? 'QT') }}" required>
          </div>
        </div>

        <div style="margin-bottom:20px">
          <label class="form-label">Logo</label>
          @if(isset($company) && $company->logo)
            <div style="margin-bottom:8px"><img src="{{ $company->getLogoUrlAttribute() }}" alt="logo" style="max-height:60px"></div>
          @endif
          <input type="file" name="logo" accept="image/*" class="form-control">
        </div>

        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:0.875rem;font-weight:500;margin-bottom:20px">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active', $company->is_active ?? true) ? 'checked' : '' }}>
          Active
        </label>

        <div style="display:flex;gap:10px">
          <button type="submit" class="btn btn-primary">{{ isset($company) ? 'Update' : 'Create Company' }}</button>
          <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
