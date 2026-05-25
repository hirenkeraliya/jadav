@extends('layouts.app')
@section('title', $invoice->invoice_number)
@section('breadcrumb')
  <a href="{{ route('invoices.index') }}" style="color:#8b5cf6;text-decoration:none">Invoices</a> / {{ $invoice->invoice_number }}
@endsection

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">{{ $invoice->invoice_number }}</h1>
    <div style="display:flex;gap:8px;margin-top:6px">
      <span class="badge badge-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
      @if($invoice->due_date && $invoice->due_date->isPast() && !in_array($invoice->status,['paid','cancelled']))
        <span class="badge badge-overdue">Overdue</span>
      @endif
    </div>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" class="btn btn-secondary">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> PDF
    </a>
    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-secondary">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
    </a>
    <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete invoice?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger btn-sm">Delete</button>
    </form>
  </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">
  <div>
    <div class="card mb-4">
      <div class="card-body">
        <div style="display:flex;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
          <div>
            <div style="font-size:0.75rem;font-weight:700;color:#8b5cf6;margin-bottom:4px">Bill To</div>
            <div style="font-weight:700;font-size:1rem;color:#1e1b4b">{{ $invoice->customer->name }}</div>
            @if($invoice->customer->organization) <div style="font-size:0.85rem;color:#6b7280">{{ $invoice->customer->organization }}</div> @endif
            @if($invoice->customer->email) <div style="font-size:0.85rem;color:#6b7280">{{ $invoice->customer->email }}</div> @endif
          </div>
          <div style="text-align:right">
            <div style="font-size:0.75rem;font-weight:700;color:#8b5cf6;margin-bottom:4px">Invoice Details</div>
            <div style="font-size:0.85rem;color:#6b7280">Date: {{ $invoice->invoice_date->format('d M Y') }}</div>
            @if($invoice->due_date) <div style="font-size:0.85rem;color:#6b7280">Due: {{ $invoice->due_date->format('d M Y') }}</div> @endif
            @if($invoice->project) <div style="font-size:0.85rem;color:#6b7280">Project: {{ $invoice->project->name }}</div> @endif
          </div>
        </div>

        <table style="width:100%;border-collapse:collapse;font-size:0.875rem;margin-bottom:16px">
          <thead>
            <tr style="border-bottom:2px solid #ede9fe">
              <th style="text-align:left;padding:8px 0;color:#8b5cf6;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">#</th>
              <th style="text-align:left;padding:8px;color:#8b5cf6;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em">Description</th>
              <th style="text-align:right;padding:8px;color:#8b5cf6;font-size:0.72rem">Qty</th>
              <th style="text-align:right;padding:8px;color:#8b5cf6;font-size:0.72rem">Rate</th>
              <th style="text-align:right;padding:8px;color:#8b5cf6;font-size:0.72rem">Tax%</th>
              <th style="text-align:right;padding:8px 0;color:#8b5cf6;font-size:0.72rem;text-transform:uppercase">Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($invoice->items as $i => $item)
            <tr style="border-bottom:1px solid #f3f4f6">
              <td style="padding:10px 0;color:#9ca3af">{{ $i+1 }}</td>
              <td style="padding:10px 8px">
                <div style="font-weight:600;color:#1e1b4b">{{ $item->name }}</div>
                @if($item->description) <div style="font-size:0.8rem;color:#9ca3af">{{ $item->description }}</div> @endif
              </td>
              <td style="text-align:right;padding:10px 8px">{{ $item->qty }}</td>
              <td style="text-align:right;padding:10px 8px">{{ $company->currency_symbol }}{{ number_format($item->unit_rate, 2) }}</td>
              <td style="text-align:right;padding:10px 8px;color:#9ca3af">{{ $item->tax_rate > 0 ? $item->tax_rate.'%' : '—' }}</td>
              <td style="text-align:right;padding:10px 0;font-weight:600">{{ $company->currency_symbol }}{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>

        <div style="display:flex;justify-content:flex-end">
          <div style="width:260px">
            <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:0.875rem">
              <span style="color:#6b7280">Subtotal</span><span>{{ $company->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            @if($invoice->discount_value > 0)
            <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:0.875rem">
              <span style="color:#6b7280">Discount</span><span style="color:#ef4444">-{{ $company->currency_symbol }}{{ number_format($invoice->discount_amount, 2) }}</span>
            </div>
            @endif
            @if($invoice->tax_rate > 0)
            <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:0.875rem">
              <span style="color:#6b7280">{{ $invoice->tax_label ?? 'Tax' }} ({{ $invoice->tax_rate }}%)</span>
              <span>{{ $company->currency_symbol }}{{ number_format($invoice->tax_amount, 2) }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-top:1px solid #e5e7eb;font-size:0.95rem">
              <span>Total</span><span style="font-weight:800;color:#6366f1">{{ $company->currency_symbol }}{{ number_format($invoice->total, 2) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:0.875rem">
              <span style="color:#10b981">Paid</span><span style="color:#10b981;font-weight:700">{{ $company->currency_symbol }}{{ number_format($invoice->paid_amount, 2) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-top:2px solid #ede9fe;font-size:1.1rem;font-weight:800">
              <span>Balance Due</span>
              <span style="color:{{ $invoice->balance_due > 0 ? '#ef4444' : '#10b981' }}">
                {{ $company->currency_symbol }}{{ number_format($invoice->balance_due, 2) }}
              </span>
            </div>
          </div>
        </div>

        @if($invoice->termsTemplate)
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6">
          <div style="font-size:0.75rem;font-weight:700;color:#8b5cf6;margin-bottom:6px">Terms & Conditions</div>
          <div style="font-size:0.8rem;color:#6b7280;white-space:pre-wrap">{{ $invoice->termsTemplate->content }}</div>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Payments sidebar --}}
  <div>
    <div class="card">
      <div class="card-header">
        <span style="font-weight:700">Payments ({{ $invoice->payments->count() }})</span>
      </div>
      <div style="max-height:240px;overflow-y:auto">
        @forelse($invoice->payments as $pay)
        <div style="padding:12px 16px;border-bottom:1px solid #f3f4f6">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div>
              <div style="font-weight:700;color:#10b981;font-size:0.95rem">{{ $company->currency_symbol }}{{ number_format($pay->amount, 2) }}</div>
              <div style="font-size:0.78rem;color:#9ca3af">{{ $pay->payment_date->format('d M Y') }}</div>
              @if($pay->paymentType) <div style="font-size:0.78rem;color:#9ca3af">{{ $pay->paymentType->name }}</div> @endif
              @if($pay->reference_number) <div style="font-size:0.78rem;color:#9ca3af">Ref: {{ $pay->reference_number }}</div> @endif
            </div>
            <form method="POST" action="{{ route('invoices.payments.delete', [$invoice, $pay]) }}" onsubmit="return confirm('Remove payment?')">
              @csrf @method('DELETE')
              <button type="submit" style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </form>
          </div>
        </div>
        @empty
        <div style="text-align:center;color:#9ca3af;padding:20px;font-size:0.85rem">No payments recorded.</div>
        @endforelse
      </div>

      @if($invoice->balance_due > 0)
      <div class="card-body" style="border-top:1px solid #ede9fe">
        <div style="font-size:0.78rem;font-weight:700;color:#8b5cf6;margin-bottom:10px">Record Payment</div>
        <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}">
          @csrf
          <div style="margin-bottom:10px">
            <input type="number" name="amount" class="form-control" step="0.01" min="0.01"
                   value="{{ $invoice->balance_due }}" placeholder="Amount" required>
          </div>
          <div style="margin-bottom:10px">
            <input type="date" name="payment_date" class="form-control" value="{{ today()->format('Y-m-d') }}" required>
          </div>
          <div style="margin-bottom:10px">
            <select name="payment_type_id" class="form-control">
              <option value="">— Method —</option>
              @foreach(\App\Models\PaymentType::where('company_id', $activeCompany->id)->get() as $pt)
                <option value="{{ $pt->id }}">{{ $pt->name }}</option>
              @endforeach
            </select>
          </div>
          <div style="margin-bottom:12px">
            <input type="text" name="reference_number" class="form-control" placeholder="Reference / Cheque no.">
          </div>
          <button type="submit" class="btn btn-success" style="width:100%">Record Payment</button>
        </form>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
