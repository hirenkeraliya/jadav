@extends('layouts.app')
@section('title', 'Settings')
@section('breadcrumb', 'Settings / Company')

@section('content')
<div style="display:flex;gap:20px;max-width:1100px">
  {{-- Settings sidebar nav --}}
  @include('partials.settings-nav')

  <div style="flex:1;min-width:0">
    <h1 class="page-title" style="margin-bottom:24px">Company Settings</h1>
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data">
          @csrf @method('PUT')

          <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:var(--color-primary);margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid var(--color-primary-border)">Company Info</div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
            <div>
              <label class="form-label">Company Name *</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
            </div>
            <div>
              <label class="form-label">Email *</label>
              <input type="email" name="email" class="form-control" value="{{ old('email', $company->email) }}" required>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
            <div>
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
            </div>
            <div>
              <label class="form-label">Website</label>
              <input type="url" name="website" class="form-control" value="{{ old('website', $company->website) }}" placeholder="https://...">
            </div>
          </div>
          <div style="margin-bottom:16px">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address', $company->address) }}</textarea>
          </div>
          <div style="margin-bottom:20px">
            <label class="form-label">Logo</label>
            @if($company->logo)
              <div style="margin-bottom:8px"><img src="{{ $company->getLogoUrlAttribute() }}" alt="logo" style="max-height:60px"></div>
            @endif
            <input type="file" name="logo" accept="image/*" class="form-control">
          </div>

          <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:var(--color-primary);margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid var(--color-primary-border)">Branding</div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
            <div>
              <label class="form-label">Primary Color</label>
              <div style="display:flex;gap:8px;align-items:center">
                <input type="color" name="primary_color" class="form-control" style="width:48px;height:38px;padding:2px;cursor:pointer" value="{{ old('primary_color', $company->primary_color ?? '#6366f1') }}">
                <input type="text" id="primaryColorText" class="form-control" style="font-family:monospace" value="{{ old('primary_color', $company->primary_color ?? '#6366f1') }}" oninput="document.querySelector('input[name=primary_color]').value=this.value">
              </div>
            </div>
            <div>
              <label class="form-label">Secondary Color</label>
              <div style="display:flex;gap:8px;align-items:center">
                <input type="color" name="secondary_color" class="form-control" style="width:48px;height:38px;padding:2px;cursor:pointer" value="{{ old('secondary_color', $company->secondary_color ?? '#f59e0b') }}">
                <input type="text" id="secondaryColorText" class="form-control" style="font-family:monospace" value="{{ old('secondary_color', $company->secondary_color ?? '#f59e0b') }}" oninput="document.querySelector('input[name=secondary_color]').value=this.value">
              </div>
            </div>
          </div>

          <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:var(--color-primary);margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid var(--color-primary-border)">Finance</div>
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px">
            <div>
              <label class="form-label">Currency</label>
              <input type="text" name="currency" class="form-control" value="{{ old('currency', $company->currency ?? 'INR') }}" placeholder="INR" required>
            </div>
            <div>
              <label class="form-label">Currency Symbol</label>
              <input type="text" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $company->currency_symbol ?? '₹') }}" placeholder="₹" required>
            </div>
            <div>
              <label class="form-label">Financial Year Start (month)</label>
              <input type="number" name="financial_year_start" class="form-control" min="1" max="12"
                     value="{{ old('financial_year_start', $company->financial_year_start ?? 4) }}" required>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
            <div>
              <label class="form-label">Tax Label</label>
              <input type="text" name="tax_label" class="form-control" value="{{ old('tax_label', $company->tax_label) }}" placeholder="GST / VAT">
            </div>
            <div>
              <label class="form-label">Tax / Registration Number</label>
              <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $company->tax_number) }}" placeholder="GSTIN">
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
            <div>
              <label class="form-label">Invoice Prefix</label>
              <input type="text" name="invoice_prefix" class="form-control" value="{{ old('invoice_prefix', $company->invoice_prefix ?? 'INV') }}" required>
            </div>
            <div>
              <label class="form-label">Quotation Prefix</label>
              <input type="text" name="quotation_prefix" class="form-control" value="{{ old('quotation_prefix', $company->quotation_prefix ?? 'QT') }}" required>
            </div>
          </div>

          <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
