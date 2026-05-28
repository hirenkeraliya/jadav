@extends('layouts.app')
@section('title', isset($quotation) ? 'Edit Quotation' : 'New Quotation')
@section('breadcrumb')
  <a href="{{ route('quotations.index') }}" style="color:#8b5cf6;text-decoration:none">Quotations</a> /
  {{ isset($quotation) ? 'Edit' : 'New' }}
@endsection

@push('styles')
<style>
  .items-table th, .items-table td { padding: 8px 10px; font-size: 0.85rem; }
  .items-table td input { padding: 6px 8px; font-size: 0.85rem; }
</style>
@endpush

@section('content')
<div style="max-width:950px">
  <h1 class="page-title" style="margin-bottom:24px">{{ isset($quotation) ? 'Edit Quotation' : 'New Quotation' }}</h1>

  <form method="POST"
        action="{{ isset($quotation) ? route('quotations.update', $quotation) : route('quotations.store') }}"
        id="quotationForm"
        data-autosave
        data-autosave-key="quotation::{{ $quotation->id ?? 'new' }}">
    @csrf
    @if(isset($quotation)) @method('PUT') @endif

    <div class="card mb-4">
      <div class="card-header"><span style="font-weight:700">Header</span></div>
      <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
          <div>
            <label class="form-label">Client <span style="color:#ef4444">*</span></label>
            <select name="customer_id" class="form-control" required>
              <option value="">— Select Client —</option>
              @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ old('customer_id', $quotation->customer_id ?? request('customer_id')) == $c->id ? 'selected' : '' }}>
                  {{ $c->name }}{{ $c->organization ? ' ('.$c->organization.')' : '' }}
                </option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="form-label">Date <span style="color:#ef4444">*</span></label>
            <input type="date" name="date" class="form-control"
                   value="{{ old('date', isset($quotation) ? $quotation->date->format('Y-m-d') : today()->format('Y-m-d')) }}" required>
          </div>
          <div>
            <label class="form-label">Valid Until</label>
            <input type="date" name="valid_until" class="form-control"
                   value="{{ old('valid_until', isset($quotation) ? $quotation->valid_until?->format('Y-m-d') : '') }}">
          </div>
        </div>
      </div>
    </div>

    {{-- Line Items --}}
    <div class="card mb-4">
      <div class="card-header">
        <span style="font-weight:700">Line Items</span>
        <button type="button" onclick="addItem()" class="btn btn-secondary btn-sm">+ Add Row</button>
      </div>
      <div class="table-wrapper">
        <table class="table items-table" id="itemsTable">
          <thead>
            <tr>
              <th style="width:35%">Description</th>
              <th>Details</th>
              <th style="width:70px">Qty</th>
              <th style="width:80px">Unit</th>
              <th style="width:110px">Rate</th>
              <th style="width:110px">Amount</th>
              <th style="width:36px"></th>
            </tr>
          </thead>
          <tbody id="itemsBody">
            @php $items = old('items', isset($quotation) ? $quotation->items->toArray() : [['name'=>'','description'=>'','qty'=>1,'unit'=>'','unit_rate'=>0,'amount'=>0]]); @endphp
            @foreach($items as $i => $item)
            <tr class="item-row">
              <td><input type="text" name="items[{{ $i }}][name]" class="form-control" value="{{ $item['name'] }}" placeholder="Item name" required></td>
              <td><input type="text" name="items[{{ $i }}][description]" class="form-control" value="{{ $item['description'] ?? '' }}" placeholder="Details"></td>
              <td><input type="number" name="items[{{ $i }}][qty]" class="form-control qty" value="{{ $item['qty'] ?? 1 }}" step="0.01" min="0" oninput="calcRow(this)"></td>
              <td><input type="text" name="items[{{ $i }}][unit]" class="form-control" value="{{ $item['unit'] ?? '' }}" placeholder="pcs"></td>
              <td><input type="number" name="items[{{ $i }}][unit_rate]" class="form-control rate" value="{{ $item['unit_rate'] ?? 0 }}" step="0.01" min="0" oninput="calcRow(this)"></td>
              <td><input type="number" name="items[{{ $i }}][amount]" class="form-control amount" value="{{ $item['amount'] ?? 0 }}" step="0.01" readonly style="background:#f9fafb"></td>
              <td>
                <button type="button" onclick="this.closest('tr').remove();calcTotals()" style="background:none;border:none;cursor:pointer;color:#ef4444;padding:4px">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- Totals + Settings --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;margin-bottom:20px">
      {{-- Notes + Terms --}}
      <div class="card">
        <div class="card-body">
          <div style="margin-bottom:14px">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $quotation->notes ?? '') }}</textarea>
          </div>
          <div style="margin-bottom:14px">
            <label class="form-label">Terms Template</label>
            <select name="terms_template_id" class="form-control">
              <option value="">— None —</option>
              @foreach($terms as $t)
                <option value="{{ $t->id }}" {{ old('terms_template_id', $quotation->terms_template_id ?? '') == $t->id ? 'selected' : '' }}>
                  {{ $t->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              @foreach(['sent' => 'Sent to Client', 'rejected' => 'Rejected', 'converted' => 'Converted'] as $value => $label)
                <option value="{{ $value }}" {{ old('status', $quotation->status ?? 'sent') == $value ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      {{-- Totals card --}}
      <div class="card">
        <div class="card-body">
          <div style="display:flex;justify-content:space-between;margin-bottom:12px">
            <span style="color:#6b7280;font-size:0.875rem">Subtotal</span>
            <span id="subtotalDisplay" style="font-weight:600">{{ $company->currency_symbol }}0</span>
          </div>

          <div style="margin-bottom:12px">
            <label class="form-label" style="font-size:0.75rem">Discount</label>
            <div style="display:flex;gap:8px">
              <select name="discount_type" id="discountType" class="form-control" style="width:120px" onchange="calcTotals()">
                <option value="percentage" {{ old('discount_type', $quotation->discount_type ?? 'percentage') == 'percentage' ? 'selected' : '' }}>%</option>
                <option value="fixed" {{ old('discount_type', $quotation->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed</option>
              </select>
              <input type="number" name="discount_value" id="discountValue" class="form-control" step="0.01" min="0"
                     value="{{ old('discount_value', $quotation->discount_value ?? 0) }}" oninput="calcTotals()">
            </div>
          </div>

          <div style="margin-bottom:12px">
            <label class="form-label" style="font-size:0.75rem">Tax</label>
            <div style="display:flex;gap:8px">
              <input type="text" name="tax_label" class="form-control" style="width:100px" placeholder="{{ $company->tax_label ?? 'Tax' }}"
                     value="{{ old('tax_label', $quotation->tax_label ?? $company->tax_label ?? '') }}">
              <input type="number" name="tax_rate" id="taxRate" class="form-control" step="0.01" min="0" max="100" placeholder="Rate %"
                     value="{{ old('tax_rate', $quotation->tax_rate ?? 0) }}" oninput="calcTotals()">
            </div>
          </div>

          <div style="border-top:2px solid #ede9fe;padding-top:12px;display:flex;justify-content:space-between">
            <span style="font-weight:800;color:#1e1b4b">Total</span>
            <span id="totalDisplay" style="font-weight:800;font-size:1.2rem;color:#6366f1">{{ $company->currency_symbol }}0</span>
          </div>
        </div>
      </div>
    </div>

    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary">
        {{ isset($quotation) ? 'Update Quotation' : 'Create Quotation' }}
      </button>
      <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
const symbol = '{{ $company->currency_symbol }}';
let rowIdx = {{ count($items) }};

function addItem() {
  const i = rowIdx++;
  const tr = document.createElement('tr');
  tr.className = 'item-row';
  tr.innerHTML = `
    <td><input type="text" name="items[${i}][name]" class="form-control" placeholder="Item name" required></td>
    <td><input type="text" name="items[${i}][description]" class="form-control" placeholder="Details"></td>
    <td><input type="number" name="items[${i}][qty]" class="form-control qty" value="1" step="0.01" min="0" oninput="calcRow(this)"></td>
    <td><input type="text" name="items[${i}][unit]" class="form-control" placeholder="pcs"></td>
    <td><input type="number" name="items[${i}][unit_rate]" class="form-control rate" value="0" step="0.01" min="0" oninput="calcRow(this)"></td>
    <td><input type="number" name="items[${i}][amount]" class="form-control amount" value="0" step="0.01" readonly style="background:#f9fafb"></td>
    <td><button type="button" onclick="this.closest('tr').remove();calcTotals()" style="background:none;border:none;cursor:pointer;color:#ef4444;padding:4px">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button></td>
  `;
  document.getElementById('itemsBody').appendChild(tr);
}

function calcRow(el) {
  const tr = el.closest('tr');
  const qty = parseFloat(tr.querySelector('.qty').value) || 0;
  const rate = parseFloat(tr.querySelector('.rate').value) || 0;
  tr.querySelector('.amount').value = (qty * rate).toFixed(2);
  calcTotals();
}

function calcTotals() {
  let subtotal = 0;
  document.querySelectorAll('.amount').forEach(el => { subtotal += parseFloat(el.value) || 0; });

  const discType = document.getElementById('discountType').value;
  const discVal = parseFloat(document.getElementById('discountValue').value) || 0;
  const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;

  const discAmt = discType === 'percentage' ? subtotal * discVal / 100 : discVal;
  const taxable = subtotal - discAmt;
  const taxAmt = taxable * taxRate / 100;
  const total = taxable + taxAmt;

  document.getElementById('subtotalDisplay').textContent = symbol + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2});
  document.getElementById('totalDisplay').textContent = symbol + total.toLocaleString(undefined, {minimumFractionDigits: 2});
}

calcTotals();
</script>
@endpush
