@extends('layouts.app')
@section('title', 'Edit Invoice — '.$project->name)
@section('breadcrumb')
  <a href="{{ route('projects.index') }}" style="color:#8b5cf6;text-decoration:none">Projects</a> /
  <a href="{{ route('projects.show', $project) }}" style="color:#8b5cf6;text-decoration:none">{{ $project->name }}</a> /
  Edit Invoice
@endsection

@section('content')
@php $currency = $activeCompany->currency_symbol; @endphp

<div style="max-width:800px" x-data="editCompletion()">
  <h1 class="page-title" style="margin-bottom:6px">Edit Invoice</h1>
  <div style="font-size:0.85rem;color:#9ca3af;margin-bottom:24px">{{ $completion->invoice_number }} · {{ $project->name }}</div>

  <form method="POST" action="{{ route('projects.completion.update', $project) }}">
    @csrf @method('PUT')

    <div class="card" style="margin-bottom:20px">
      <div class="card-header">
        <span style="font-weight:700">Line Items</span>
        <button type="button" @click="addItem()" class="btn btn-secondary btn-sm">+ Add Line</button>
      </div>
      <div class="card-body" style="padding:16px">
        <div style="display:grid;grid-template-columns:3fr 80px 120px 120px 36px;gap:8px;margin-bottom:8px">
          <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af">Description</div>
          <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af">Qty</div>
          <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af">Rate</div>
          <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:#9ca3af;text-align:right">Amount</div>
          <div></div>
        </div>

        <template x-for="(item, i) in items" :key="i">
          <div style="display:grid;grid-template-columns:3fr 80px 120px 120px 36px;gap:8px;margin-bottom:8px;align-items:center">
            <input type="text" :name="'items['+i+'][description]'" x-model="item.description"
                   class="form-control" placeholder="Description" required>
            <input type="number" :name="'items['+i+'][qty]'" x-model.number="item.qty"
                   class="form-control" min="0.01" step="0.01" required>
            <input type="number" :name="'items['+i+'][rate]'" x-model.number="item.rate"
                   class="form-control" min="0" step="0.01" required>
            <div style="text-align:right;font-weight:600;font-size:0.9rem"
                 x-text="'{{ $currency }}' + fmt(item.qty * item.rate)"></div>
            <button type="button" @click="items.length > 1 && items.splice(i,1)"
                    style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
        </template>

        <div style="display:flex;justify-content:flex-end;margin-top:16px;padding-top:12px;border-top:1px solid #f3f4f6">
          <div style="width:220px">
            <div style="display:flex;justify-content:space-between;font-size:1rem;font-weight:800;color:#1e1b4b">
              <span>Total</span>
              <span x-text="'{{ $currency }}' + fmt(total())"></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card" style="margin-bottom:20px">
      <div class="card-header"><span style="font-weight:700">Terms &amp; Conditions</span></div>
      <div class="card-body">
        <label class="form-label">Template</label>
        <select name="terms_template_id" class="form-control" x-model="termsId">
          <option value="">— None —</option>
          @foreach($termsTemplates as $t)
            <option value="{{ $t->id }}">{{ $t->name }}{{ $t->is_default_invoice ? ' (default)' : '' }}</option>
          @endforeach
        </select>
        <div x-show="termsContent()" x-cloak
             style="margin-top:12px;padding:12px 14px;background:#fafaf9;border:1px solid #e5e7eb;border-radius:10px;font-size:0.82rem;color:#4b5563;line-height:1.6;white-space:pre-wrap"
             x-text="termsContent()"></div>
        @if($termsTemplates->isEmpty())
          <div style="margin-top:10px;font-size:0.8rem;color:#9ca3af">
            No templates yet. Add one in <a href="{{ route('settings.terms.index') }}" style="color:var(--color-primary);text-decoration:none">Settings → Terms Templates</a>.
          </div>
        @endif
      </div>
    </div>

    <div class="card" style="margin-bottom:20px">
      <div class="card-body">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $completion->notes) }}</textarea>
      </div>
    </div>

    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary">Save Invoice</button>
      <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
function editCompletion() {
  return {
    items: @json($completion->items->map(fn($i) => ['description'=>$i->description,'qty'=>(float)$i->qty,'rate'=>(float)$i->rate])),
    termsId: '{{ old('terms_template_id', $completion->terms_template_id) }}',
    termsMap: @json($termsTemplates->pluck('content', 'id')),
    addItem() { this.items.push({ description: '', qty: 1, rate: 0 }); },
    total() { return this.items.reduce((s,i) => s + (parseFloat(i.qty)||0)*(parseFloat(i.rate)||0), 0); },
    fmt(n) { return Number(n).toLocaleString(undefined,{minimumFractionDigits:0,maximumFractionDigits:0}); },
    termsContent() { return this.termsId ? (this.termsMap[this.termsId] || '') : ''; },
  };
}
</script>
@endpush
@endsection
