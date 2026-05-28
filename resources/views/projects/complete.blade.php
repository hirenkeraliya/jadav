@extends('layouts.app')
@section('title', 'Complete Project — '.$project->name)
@section('breadcrumb')
  <a href="{{ route('projects.index') }}" style="color:#8b5cf6;text-decoration:none">Projects</a> /
  <a href="{{ route('projects.show', $project) }}" style="color:#8b5cf6;text-decoration:none">{{ $project->name }}</a> /
  Complete
@endsection

@section('content')
@php
  $currency = $activeCompany->currency_symbol;
  $due      = max(0, $totalExpense - $totalReceived); // pre-invoice due based on finance entries
@endphp

<div style="max-width:960px" x-data="completionForm()">

  <div style="display:flex;align-items:center;gap:14px;margin-bottom:24px">
    <div style="width:44px;height:44px;background:#d1fae5;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <div>
      <h1 class="page-title" style="margin:0">Complete Project</h1>
      <div style="font-size:0.85rem;color:#6b7280;margin-top:2px">{{ $project->project_code }} · {{ $project->name }}</div>
    </div>
  </div>

  <form method="POST" action="{{ route('projects.complete.store', $project) }}">
  @csrf

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">

    {{-- Finance summary --}}
    <div class="card">
      <div class="card-header"><span style="font-weight:700">Finance Summary</span></div>
      <div class="card-body">
        <dl style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div style="background:#f0fdf4;border-radius:10px;padding:14px;text-align:center">
            <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#10b981;margin-bottom:4px">Total Received</div>
            <div style="font-size:1.4rem;font-weight:800;color:#065f46">{{ $currency }}{{ number_format($totalReceived, 0) }}</div>
          </div>
          <div style="background:#fef2f2;border-radius:10px;padding:14px;text-align:center">
            <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#ef4444;margin-bottom:4px">Total Expense</div>
            <div style="font-size:1.4rem;font-weight:800;color:#991b1b">{{ $currency }}{{ number_format($totalExpense, 0) }}</div>
          </div>
        </dl>
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6">
          <div style="font-size:0.8rem;color:#6b7280;margin-bottom:8px">Finance Entries</div>
          <div style="max-height:180px;overflow-y:auto">
            @foreach($financeEntries as $e)
            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f9fafb;font-size:0.82rem">
              <span style="color:#6b7280">{{ $e->date->format('d M Y') }} · {{ $e->entryType->name ?? '—' }}</span>
              <span style="font-weight:600;color:{{ $e->type==='credit' ? '#10b981' : '#ef4444' }}">
                {{ $e->type==='credit' ? '+' : '-' }}{{ $currency }}{{ number_format($e->amount, 0) }}
              </span>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- Final expenses --}}
    <div class="card">
      <div class="card-header">
        <span style="font-weight:700">Add Final Expenses</span>
        <button type="button" @click="addExpense()" class="btn btn-secondary btn-sm">+ Add</button>
      </div>
      <div class="card-body" style="padding:12px">
        <template x-for="(exp, i) in expenses" :key="i">
          <div style="display:grid;grid-template-columns:1fr 1fr 80px auto;gap:8px;margin-bottom:10px;align-items:start">
            <div>
              <label class="form-label" style="font-size:0.72rem">Description</label>
              <input type="text" :name="'expenses['+i+'][remarks]'" x-model="exp.remarks"
                     class="form-control" placeholder="Description">
            </div>
            <div>
              <label class="form-label" style="font-size:0.72rem">Date</label>
              <input type="date" :name="'expenses['+i+'][date]'" x-model="exp.date" class="form-control">
            </div>
            <div>
              <label class="form-label" style="font-size:0.72rem">Amount</label>
              <input type="number" :name="'expenses['+i+'][amount]'" x-model="exp.amount"
                     class="form-control" step="0.01" min="0" placeholder="0">
            </div>
            <div style="padding-top:22px">
              <button type="button" @click="expenses.splice(i,1)"
                      style="background:none;border:none;cursor:pointer;color:#ef4444;padding:4px">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>
          </div>
        </template>
        <div x-show="expenses.length === 0" style="text-align:center;color:#9ca3af;font-size:0.85rem;padding:20px 0">
          No additional expenses. Click + Add if needed.
        </div>
      </div>
    </div>

  </div>

  {{-- Invoice builder --}}
  <div class="card" style="margin-bottom:20px">
    <div class="card-header">
      <span style="font-weight:700">Completion Invoice</span>
      <div style="display:flex;gap:10px;align-items:center">
        <div>
          <label class="form-label" style="margin:0;font-size:0.75rem">Invoice Number</label>
          <input type="text" name="invoice_number" class="form-control" style="width:180px;font-size:0.85rem"
                 value="{{ old('invoice_number', $suggestedInvoiceNumber) }}" required>
        </div>
        <button type="button" @click="addItem()" class="btn btn-secondary btn-sm" style="flex-shrink:0;margin-top:14px">+ Line Item</button>
      </div>
    </div>
    <div class="card-body" style="padding:16px">
      @error('items') <div style="color:#ef4444;font-size:0.85rem;margin-bottom:10px">{{ $message }}</div> @enderror

      {{-- Header row --}}
      <div style="display:grid;grid-template-columns:3fr 80px 120px 120px 36px;gap:8px;margin-bottom:8px">
        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af">Description</div>
        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af">Qty</div>
        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af">Rate</div>
        <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;text-align:right">Amount</div>
        <div></div>
      </div>

      <template x-for="(item, i) in items" :key="i">
        <div style="display:grid;grid-template-columns:3fr 80px 120px 120px 36px;gap:8px;margin-bottom:8px;align-items:center">
          <input type="text" :name="'items['+i+'][description]'" x-model="item.description"
                 class="form-control" placeholder="Work description…" required>
          <input type="number" :name="'items['+i+'][qty]'" x-model.number="item.qty"
                 @input="calcItem(item)" class="form-control" min="0.01" step="0.01" required>
          <input type="number" :name="'items['+i+'][rate]'" x-model.number="item.rate"
                 @input="calcItem(item)" class="form-control" min="0" step="0.01" required placeholder="0.00">
          <div style="text-align:right;font-weight:600;font-size:0.9rem;padding-right:4px"
               x-text="'{{ $currency }}' + formatNum(item.qty * item.rate)"></div>
          <button type="button" @click="items.length > 1 && items.splice(i,1)"
                  style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
      </template>

      {{-- Totals --}}
      <div style="display:flex;justify-content:flex-end;margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6">
        <div style="width:260px">
          <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:0.875rem">
            <span style="color:#6b7280">Invoice Total</span>
            <span style="font-weight:700" x-text="'{{ $currency }}' + formatNum(invoiceTotal())"></span>
          </div>
          <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:0.875rem">
            <span style="color:#10b981">Already Received</span>
            <span style="color:#10b981;font-weight:600">- {{ $currency }}{{ number_format($totalReceived, 0) }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;padding-top:8px;border-top:2px solid #1e1b4b;font-size:1rem">
            <span style="font-weight:800;color:#1e1b4b">Due Amount</span>
            <span style="font-weight:800;color:#ef4444" x-text="'{{ $currency }}' + formatNum(Math.max(0, invoiceTotal() - {{ $totalReceived }}))"></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Notes --}}
  <div class="card" style="margin-bottom:24px">
    <div class="card-body">
      <label class="form-label">Invoice Notes</label>
      <textarea name="notes" class="form-control" rows="2"
                placeholder="Thank you for your business…">{{ old('notes') }}</textarea>
    </div>
  </div>

  {{-- Actions --}}
  <div style="display:flex;gap:12px;align-items:center">
    <button type="submit" class="btn btn-primary"
            style="background:#10b981;border-color:#10b981;padding:10px 28px;font-size:0.95rem;font-weight:700"
            onclick="return confirm('Confirm project completion? This will mark the project as Completed.')">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      Confirm Completion
    </button>
    <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Cancel</a>
  </div>

  </form>
</div>

@push('scripts')
<script>
function completionForm() {
  return {
    items: [{ description: '', qty: 1, rate: 0 }],
    expenses: [],
    addItem() {
      this.items.push({ description: '', qty: 1, rate: 0 });
    },
    addExpense() {
      this.expenses.push({ remarks: '', date: '{{ date('Y-m-d') }}', amount: '' });
    },
    calcItem(item) {
      // reactive — binding handles it
    },
    invoiceTotal() {
      return this.items.reduce((s, it) => s + (parseFloat(it.qty)||0) * (parseFloat(it.rate)||0), 0);
    },
    formatNum(n) {
      return Number(n).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    },
  };
}
</script>
@endpush
@endsection
