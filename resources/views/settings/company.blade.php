@extends('layouts.app')
@section('title', 'Settings')
@section('breadcrumb', 'Settings / Company')

@push('styles')
<style>
  .settings-page-wrap {
    display: flex;
    gap: 24px;
    max-width: 1140px;
    align-items: flex-start;
  }
  .settings-main { flex: 1; min-width: 0; }

  /* Settings hero */
  .settings-hero {
    background: linear-gradient(135deg,
      color-mix(in srgb, var(--color-primary) 90%, #000) 0%,
      var(--color-primary) 55%,
      color-mix(in srgb, var(--color-primary) 70%, var(--color-secondary)) 100%
    );
    border-radius: 18px;
    padding: 24px 28px;
    margin-bottom: 24px;
    color: #fff;
    position: relative;
    overflow: hidden;
  }
  .settings-hero::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,0.07);
    border-radius: 50%;
  }
  .settings-hero-content { position: relative; z-index: 1; }
  .settings-hero h1 { font-size: 1.4rem; font-weight: 800; letter-spacing: -0.02em; margin: 0; display: flex; align-items: center; gap: 10px; }
  .settings-hero p  { font-size: 0.82rem; opacity: 0.8; margin: 5px 0 0; }

  /* Section blocks */
  .form-section { padding: 22px 24px; border-bottom: 1px solid #f3f4f6; }
  .form-section:last-of-type { border-bottom: none; }
  .form-section-label {
    display: flex; align-items: center; gap: 9px;
    font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.1em; color: var(--color-primary);
    margin-bottom: 18px;
  }
  .form-section-label::after {
    content: ''; flex: 1; height: 1px;
    background: var(--color-primary-border);
  }
  .form-section-icon {
    width: 24px; height: 24px; border-radius: 6px;
    background: var(--color-primary-subtle);
    display: flex; align-items: center; justify-content: center;
    color: var(--color-primary); flex-shrink: 0;
  }
  .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
  @media (max-width: 640px) {
    .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
  }

  /* Color picker row */
  .color-input-row { display: flex; gap: 8px; align-items: center; }
  .color-swatch {
    width: 38px; height: 38px;
    border-radius: 10px; border: 2px solid #e5e7eb;
    cursor: pointer; overflow: hidden; flex-shrink: 0;
  }
  .color-swatch input[type=color] {
    width: 100%; height: 100%; border: none; padding: 0; cursor: pointer;
    background: none;
  }
  .color-hex {
    flex: 1; font-family: 'Courier New', monospace;
    font-size: 0.85rem;
  }
  .color-preview-dot {
    width: 12px; height: 12px; border-radius: 50%;
    flex-shrink: 0;
    box-shadow: 0 0 0 2px #fff, 0 0 0 3px #e5e7eb;
  }

  /* Logo preview */
  .logo-preview-box {
    display: flex; align-items: center; gap: 14px;
    padding: 12px 16px;
    background: #fafaf9;
    border: 1.5px dashed #e5e7eb;
    border-radius: 12px;
    margin-bottom: 12px;
    transition: border-color 0.2s;
  }
  .logo-preview-box:hover { border-color: var(--color-primary); }
  .logo-preview-box img { max-height: 48px; max-width: 120px; object-fit: contain; }
  .logo-placeholder {
    width: 48px; height: 48px; border-radius: 10px;
    background: var(--color-primary-subtle);
    display: flex; align-items: center; justify-content: center;
    color: var(--color-primary);
  }

  /* Month selector */
  .month-select { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
  .month-btn {
    padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 600;
    border: 1.5px solid #e5e7eb; background: #fff; color: #6b7280;
    cursor: pointer; transition: all 0.15s;
  }
  .month-btn.active { border-color: var(--color-primary); background: var(--color-primary-subtle); color: var(--color-primary); }

  /* Form footer */
  .form-footer {
    padding: 18px 24px;
    border-top: 1px solid #f3f4f6;
    display: flex; align-items: center; gap: 10px;
    background: #fafaf9; border-radius: 0 0 16px 16px;
  }
</style>
@endpush

@section('content')
<div class="settings-page-wrap">
  @include('partials.settings-nav')

  <div class="settings-main">

    {{-- Hero --}}
    <div class="settings-hero">
      <div class="settings-hero-content">
        <h1>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Company Settings
        </h1>
        <p>Manage your company profile, branding and financial preferences</p>
      </div>
    </div>

    @if(session('success'))
    <div style="background:#d1fae5;border:1px solid #a7f3d0;color:#065f46;padding:12px 16px;border-radius:12px;margin-bottom:20px;font-size:0.875rem;font-weight:600;display:flex;align-items:center;gap:8px">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data">
      @csrf @method('PUT')

      <div class="card" style="overflow:visible">

        {{-- Company Info --}}
        <div class="form-section">
          <div class="form-section-label">
            <span class="form-section-icon">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/></svg>
            </span>
            Company Info
          </div>
          <div class="form-grid-2" style="margin-bottom:16px">
            <div>
              <label class="form-label">Company Name *</label>
              <input type="text" name="name" class="form-control @error('name') error @enderror"
                     value="{{ old('name', $company->name) }}" required placeholder="Acme Studio">
              @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
              <label class="form-label">Email *</label>
              <input type="email" name="email" class="form-control @error('email') error @enderror"
                     value="{{ old('email', $company->email) }}" required placeholder="hello@company.com">
              @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>
          </div>
          <div class="form-grid-2" style="margin-bottom:16px">
            <div>
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control"
                     value="{{ old('phone', $company->phone) }}" placeholder="+1 (555) 000-0000">
            </div>
            <div>
              <label class="form-label">Website</label>
              <input type="url" name="website" class="form-control"
                     value="{{ old('website', $company->website) }}" placeholder="https://...">
            </div>
          </div>
          <div>
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"
                      placeholder="123 Design Street, City, State">{{ old('address', $company->address) }}</textarea>
          </div>
        </div>

        {{-- Logo --}}
        <div class="form-section">
          <div class="form-section-label">
            <span class="form-section-icon">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </span>
            Logo
          </div>
          @if($company->logo)
          <div class="logo-preview-box">
            <img src="{{ $company->getLogoUrlAttribute() }}" alt="Company logo">
            <div>
              <div style="font-size:0.82rem;font-weight:600;color:#374151">Current Logo</div>
              <div style="font-size:0.75rem;color:#9ca3af;margin-top:2px">Upload a new file to replace it</div>
            </div>
          </div>
          @else
          <div class="logo-preview-box" style="margin-bottom:12px">
            <div class="logo-placeholder">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </div>
            <div style="font-size:0.82rem;color:#9ca3af">No logo uploaded yet</div>
          </div>
          @endif
          <input type="file" name="logo" accept="image/*" class="form-control" style="max-width:360px">
          <p style="font-size:0.75rem;color:#9ca3af;margin:6px 0 0">PNG, JPG or SVG · Max 2 MB</p>
        </div>

        {{-- QR Code --}}
        <div class="form-section">
          <div class="form-section-label">
            <span class="form-section-icon">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="3" height="3"/><rect x="18" y="14" width="3" height="3"/><rect x="14" y="18" width="3" height="3"/><rect x="18" y="18" width="3" height="3"/></svg>
            </span>
            QR Code
          </div>
          <p style="font-size:0.82rem;color:#6b7280;margin-bottom:14px">Upload a QR code (e.g. payment / bank details) to be printed on invoices and quotations.</p>
          @if($company->qr_code)
          <div class="logo-preview-box" style="margin-bottom:12px">
            <img src="{{ $company->getQrCodeUrlAttribute() }}" alt="QR Code" style="max-height:80px;max-width:80px;object-fit:contain">
            <div>
              <div style="font-size:0.82rem;font-weight:600;color:#374151">Current QR Code</div>
              <div style="font-size:0.75rem;color:#9ca3af;margin-top:2px">Upload a new file to replace it, or remove it below</div>
            </div>
          </div>
          <label style="display:inline-flex;align-items:center;gap:6px;font-size:0.8rem;color:#ef4444;cursor:pointer;margin-bottom:12px">
            <input type="checkbox" name="remove_qr_code" value="1" style="accent-color:#ef4444">
            Remove current QR code
          </label>
          @else
          <div class="logo-preview-box" style="margin-bottom:12px">
            <div class="logo-placeholder">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </div>
            <div style="font-size:0.82rem;color:#9ca3af">No QR code uploaded yet</div>
          </div>
          @endif
          <input type="file" name="qr_code" accept="image/*" class="form-control" style="max-width:360px">
          <p style="font-size:0.75rem;color:#9ca3af;margin:6px 0 0">PNG or JPG · Max 2 MB · Recommended: square image (e.g. 400×400)</p>
        </div>

        {{-- Branding --}}
        <div class="form-section">
          <div class="form-section-label">
            <span class="form-section-icon">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 0-14.14 0M4.93 19.07a10 10 0 0 0 14.14 0"/></svg>
            </span>
            Branding
          </div>
          <div class="form-grid-2">
            <div>
              <label class="form-label">Primary Color</label>
              <div class="color-input-row">
                <div class="color-swatch" id="primarySwatchWrap" style="border-color: {{ old('primary_color', $company->primary_color ?? '#6366f1') }}">
                  <input type="color" name="primary_color" id="primaryColor"
                         value="{{ old('primary_color', $company->primary_color ?? '#6366f1') }}"
                         oninput="syncColor('primary')">
                </div>
                <input type="text" id="primaryColorText" class="form-control color-hex"
                       value="{{ old('primary_color', $company->primary_color ?? '#6366f1') }}"
                       maxlength="7" placeholder="#6366f1"
                       oninput="syncColorText('primary')">
              </div>
            </div>
            <div>
              <label class="form-label">Secondary Color</label>
              <div class="color-input-row">
                <div class="color-swatch" id="secondarySwatchWrap" style="border-color: {{ old('secondary_color', $company->secondary_color ?? '#f59e0b') }}">
                  <input type="color" name="secondary_color" id="secondaryColor"
                         value="{{ old('secondary_color', $company->secondary_color ?? '#f59e0b') }}"
                         oninput="syncColor('secondary')">
                </div>
                <input type="text" id="secondaryColorText" class="form-control color-hex"
                       value="{{ old('secondary_color', $company->secondary_color ?? '#f59e0b') }}"
                       maxlength="7" placeholder="#f59e0b"
                       oninput="syncColorText('secondary')">
              </div>
            </div>
          </div>
          <p style="font-size:0.75rem;color:#9ca3af;margin:10px 0 0">Colors are applied across the interface — sidebar, buttons, badges and accent elements.</p>
        </div>

        {{-- Finance --}}
        <div class="form-section">
          <div class="form-section-label">
            <span class="form-section-icon">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </span>
            Finance &amp; Prefixes
          </div>
          <div class="form-grid-3" style="margin-bottom:16px">
            <div>
              <label class="form-label">Currency Code</label>
              <input type="text" name="currency" class="form-control"
                     value="{{ old('currency', $company->currency ?? 'INR') }}"
                     placeholder="INR" required maxlength="10">
            </div>
            <div>
              <label class="form-label">Currency Symbol</label>
              <input type="text" name="currency_symbol" class="form-control"
                     value="{{ old('currency_symbol', $company->currency_symbol ?? '₹') }}"
                     placeholder="₹" required maxlength="5">
            </div>
            <div>
              <label class="form-label">Invoice Prefix</label>
              <input type="text" name="invoice_prefix" class="form-control"
                     value="{{ old('invoice_prefix', $company->invoice_prefix ?? 'INV') }}"
                     required maxlength="20" placeholder="INV">
            </div>
          </div>
          <div class="form-grid-3" style="margin-bottom:16px">
            <div>
              <label class="form-label">Quotation Prefix</label>
              <input type="text" name="quotation_prefix" class="form-control"
                     value="{{ old('quotation_prefix', $company->quotation_prefix ?? 'QT') }}"
                     required maxlength="20" placeholder="QT">
            </div>
            <div>
              <label class="form-label">Tax Label</label>
              <input type="text" name="tax_label" class="form-control"
                     value="{{ old('tax_label', $company->tax_label) }}"
                     placeholder="GST / VAT">
            </div>
            <div>
              <label class="form-label">Tax / Reg. Number</label>
              <input type="text" name="tax_number" class="form-control"
                     value="{{ old('tax_number', $company->tax_number) }}"
                     placeholder="GSTIN / TIN">
            </div>
          </div>
          <div>
            <label class="form-label">Financial Year Start</label>
            @php
              $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
              $currentFY = (int) old('financial_year_start', $company->financial_year_start ?? 4);
            @endphp
            <input type="hidden" name="financial_year_start" id="fyMonth" value="{{ $currentFY }}">
            <div class="month-select">
              @foreach($months as $i => $m)
              <button type="button" class="month-btn {{ ($i+1) === $currentFY ? 'active' : '' }}"
                      data-month="{{ $i+1 }}"
                      onclick="setFyMonth(this, {{ $i+1 }})">{{ $m }}</button>
              @endforeach
            </div>
            <p style="font-size:0.75rem;color:#9ca3af;margin:8px 0 0">Month when your financial year begins.</p>
          </div>
        </div>

        {{-- Submit --}}
        <div class="form-footer">
          <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Save Settings
          </button>
        </div>

      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function syncColor(key) {
  const picker = document.getElementById(key + 'Color');
  const text   = document.getElementById(key + 'ColorText');
  const swatch = document.getElementById(key + 'SwatchWrap');
  text.value = picker.value;
  swatch.style.borderColor = picker.value;
}
function syncColorText(key) {
  const text   = document.getElementById(key + 'ColorText');
  const picker = document.getElementById(key + 'Color');
  const swatch = document.getElementById(key + 'SwatchWrap');
  if (/^#[0-9a-fA-F]{6}$/.test(text.value)) {
    picker.value = text.value;
    swatch.style.borderColor = text.value;
  }
}
function setFyMonth(btn, month) {
  document.getElementById('fyMonth').value = month;
  document.querySelectorAll('.month-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}
</script>
@endpush
