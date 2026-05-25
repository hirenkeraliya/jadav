@extends('layouts.app')
@section('title', isset($invoice) ? 'Edit Invoice' : 'New Invoice')
@section('breadcrumb')
  <a href="{{ route('invoices.index') }}" style="color:#8b5cf6;text-decoration:none">Invoices</a> /
  {{ isset($invoice) ? 'Edit' : 'New' }}
@endsection

@push('styles')
<style>.items-table td input { padding:6px 8px;font-size:0.85rem; }</style>
@endpush

@section('content')
<div style="max-width:950px">
  <h1 class="page-title" style="margin-bottom:24px">{{ isset($invoice) ? 'Edit Invoice' : 'New Invoice' }}</h1>

  <form method="POST"
        action="{{ isset($invoice) ? route('invoices.update', $invoice) : route('invoices.store') }}"
        id="invoiceForm">
    @csrf
    @if(isset($invoice)) @method('PUT') @endif

    <div class="card mb-4">
      <div class="card-header"><span style="font-weight:700">Header</span></div>
      <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Customer <span style="color:#ef4444">*</span></label>
            <select name="customer_id" class="form-control" required>
              <option value="">— Select Customer —</option>
              @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ old('customer_id', $invoice->customer_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="form-label">Project</label>
            <select name="project_id" class="form-control">
              <option value="">— None —</option>
              @foreach($projects as $p)
                <option value="{{ $p->id }}" {{ old('project_id', $invoice->project_id ?? request('project_id')) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
          <div>
            <label class="form-label">Invoice Date <span style="color:#ef4444">*</span></label>
            <input type="date" name="invoice_date" class="form-control"
                   value="{{ old('invoice_date', isset($invoice) ? $invoice->invoice_date->format('Y-m-d') : today()->format('Y-m-d')) }}" required>
          </div>
          <div>
            <label class="form-label">Due Date</label>
            <input type="date" name="due_date" class="form-control"
                   value="{{ old('due_date', isset($invoice) ? $invoice->due_date?->format('Y-m-d') : '') }}">
          </div>
          <div>
            <label class="form-label">Template</label>
            <select name="template" class="form-control">
              <option value="default" {{ old('template', $invoice->template ?? 'default') == 'default' ? 'selected' : '' }}>Default</option>
              <option value="modern" {{ old('template', $invoice->template ?? '') == 'modern' ? 'selected' : '' }}>Modern</option>
              <option value="minimal" {{ old('template', $invoice->template ?? '') == 'minimal' ? 'selected' : '' }}>Minimal</option>
            </select>
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
            <tr><th style="width:32%">Description</th><th>Details</th><th style="width:70px">Qty</th><th style="width:80px">Unit</th><th style="width:100px">Rate</th><th style="width:80px">Tax%</th><th style="width:110px">Amount</th><th style="width:36px"></th></tr>
          </thead>
          <tbody id="itemsBody">
            @php $items = old('items', isset($invoice) ? $invoice->items->toArray() : [['name'=>'','description'=>'','qty'=>1,'unit'=>'','unit_rate'=>0,'tax_rate'=>0,'amount'=>0]]); @endphp
            @foreach($items as $i => $item)
            <tr class="item-row">
              <td><input type="text" name="items[{{ $i }}][name]" class="form-control" value="{{ $item['name'] }}" placeholder="Item name" required></td>
              <td><input type="text" name="items[{{ $i }}][description]" class="form-control" value="{{ $item['description'] ?? '' }}" placeholder="Details"></td>
              <td><input type="number" name="items[{{ $i }}][qty]" class="form-control qty" value="{{ $item['qty'] ?? 1 }}" step="0.01" min="0" oninput="calcRow(this)"></td>
              <td><input type="text" name="items[{{ $i }}][unit]" class="form-control" value="{{ $item['unit'] ?? '' }}" placeholder="pcs"></td>
              <td><input type="number" name="items[{{ $i }}][unit_rate]" class="form-control rate" value="{{ $item['unit_rate'] ?? 0 }}" step="0.01" min="0" oninput="calcRow(this)"></td>
              <td><input type="number" name="items[{{ $i }}][tax_rate]" class="form-control" value="{{ $item['tax_rate'] ?? 0 }}" step="0.01" min="0" max="100"></td>
              <td><input type="number" name="items[{{ $i }}][amount]" class="form-control amount" value="{{ $item['amount'] ?? 0 }}" step="0.01" readonly style="background:#f9fafb"></td>
              <td><button type="button" onclick="this.closest('tr').remove();calcTotals()" style="background:none;border:none;cursor:pointer;color:#ef4444;padding:4px">✕</button></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;margin-bottom:20px">
      <div class="card">
        <div class="card-body">
          <div style="margin-bottom:14px">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $invoice->notes ?? '') }}</textarea>
          </div>
          <div>
            <label class="form-label">Terms Template</label>
            <select name="terms_template_id" class="form-control">
              <option value="">— None —</option>
              @foreach($terms as $t)
                <option value="{{ $t->id }}" {{ old('terms_template_id', $invoice->terms_template_id ?? '') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div style="display:flex;justify-content:space-between;margin-bottom:12px">
            <span style="color:#6b7280;font-size:0.875rem">Subtotal</span>
            <span id="subtotalDisplay" style="font-weight:600">{{ $company->currency_symbol }}0</span>
          </div>
          <div style="margin-bottom:12px">
            <label class="form-label" style="font-size:0.75rem">Discount</label>
            <div style="display:flex;gap:8px">
              <select name="discount_type" id="discountType" class="form-control" style="width:110px" onchange="calcTotals()">
                <option value="percentage" {{ old('discount_type', $invoice->discount_type ?? 'percentage') == 'percentage' ? 'selected' : '' }}>%</option>
                <option value="fixed" {{ old('discount_type', $invoice->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed</option>
              </select>
              <input type="number" name="discount_value" id="discountValue" class="form-control" step="0.01" min="0"
                     value="{{ old('discount_value', $invoice->discount_value ?? 0) }}" oninput="calcTotals()">
            </div>
          </div>
          <div style="margin-bottom:12px">
            <label class="form-label" style="font-size:0.75rem">Tax</label>
            <div style="display:flex;gap:8px">
              <input type="text" name="tax_label" class="form-control" style="width:90px" placeholder="{{ $company->tax_label ?? 'Tax' }}"
                     value="{{ old('tax_label', $invoice->tax_label ?? $company->tax_label ?? '') }}">
              <input type="number" name="tax_rate" id="taxRate" class="form-control" step="0.01" min="0" max="100" placeholder="Rate %"
                     value="{{ old('tax_rate', $invoice->tax_rate ?? 0) }}" oninput="calcTotals()">
            </div>
          </div>
          <div style="border-top:2px solid #ede9fe;padding-top:12px;display:flex;justify-content:space-between">
            <span style="font-weight:800">Total</span>
            <span id="totalDisplay" style="font-weight:800;font-size:1.2rem;color:#6366f1">{{ $company->currency_symbol }}0</span>
          </div>
        </div>
      </div>
    </div>

    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary">{{ isset($invoice) ? 'Update Invoice' : 'Create Invoice' }}</button>
      <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
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
  tr.innerHTML = `<td><input type="text" name="items[${i}][name]" class="form-control" placeholder="Item name" required></td><td><input type="text" name="items[${i}][description]" class="form-control" placeholder="Details"></td><td><input type="number" name="items[${i}][qty]" class="form-control qty" value="1" step="0.01" min="0" oninput="calcRow(this)"></td><td><input type="text" name="items[${i}][unit]" class="form-control" placeholder="pcs"></td><td><input type="number" name="items[${i}][unit_rate]" class="form-control rate" value="0" step="0.01" min="0" oninput="calcRow(this)"></td><td><input type="number" name="items[${i}][tax_rate]" class="form-control" value="0" step="0.01" min="0" max="100"></td><td><input type="number" name="items[${i}][amount]" class="form-control amount" value="0" step="0.01" readonly style="background:#f9fafb"></td><td><button type="button" onclick="this.closest('tr').remove();calcTotals()" style="background:none;border:none;cursor:pointer;color:#ef4444;padding:4px">✕</button></td>`;
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
  const total = taxable + taxable * taxRate / 100;
  document.getElementById('subtotalDisplay').textContent = symbol + subtotal.toLocaleString(undefined, {minimumFractionDigits:2});
  document.getElementById('totalDisplay').textContent = symbol + total.toLocaleString(undefined, {minimumFractionDigits:2});
}
calcTotals();
</script>
@endpush
