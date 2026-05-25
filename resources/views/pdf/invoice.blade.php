<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
  * { margin:0;padding:0;box-sizing:border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1e1b4b; background:#fff; }
  .page { padding: 36px 40px; }
  .header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:30px; padding-bottom:20px; border-bottom:3px solid {{ $company->primary_color ?? '#6366f1' }}; }
  .company-logo img { max-height:60px; max-width:180px; }
  .company-name { font-size:20px; font-weight:800; color:{{ $company->primary_color ?? '#6366f1' }}; }
  .company-details { font-size:9.5px; color:#6b7280; margin-top:4px; line-height:1.6; }
  .doc-title { text-align:right; }
  .doc-title h1 { font-size:24px; font-weight:800; color:{{ $company->primary_color ?? '#6366f1' }}; text-transform:uppercase; letter-spacing:2px; }
  .doc-number { font-size:13px; font-weight:700; color:#1e1b4b; margin-top:4px; }
  .doc-meta { font-size:9.5px; color:#6b7280; margin-top:2px; }
  .billing { display:flex; justify-content:space-between; margin-bottom:24px; }
  .bill-box { width:48%; }
  .bill-box h3 { font-size:8px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:{{ $company->primary_color ?? '#6366f1' }}; margin-bottom:7px; }
  .bill-box p { font-size:10px; color:#374151; line-height:1.6; }
  .status-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:1px; }
  .status-draft { background:#f3f4f6; color:#6b7280; }
  .status-sent { background:#ede9fe; color:#6366f1; }
  .status-paid { background:#d1fae5; color:#065f46; }
  .status-partial { background:#fef3c7; color:#92400e; }
  .status-overdue { background:#fee2e2; color:#991b1b; }
  table { width:100%; border-collapse:collapse; margin-bottom:16px; }
  thead tr { background:{{ $company->primary_color ?? '#6366f1' }}; }
  thead th { color:#fff; font-size:9px; font-weight:700; text-transform:uppercase; padding:8px 10px; text-align:left; letter-spacing:0.5px; }
  thead th:last-child, thead th:nth-child(3), thead th:nth-child(4), thead th:nth-child(5) { text-align:right; }
  tbody tr:nth-child(even) { background:#f9fafb; }
  tbody td { padding:9px 10px; font-size:10px; border-bottom:1px solid #f3f4f6; vertical-align:top; }
  .item-name { font-weight:700; color:#1e1b4b; }
  .item-desc { font-size:8.5px; color:#9ca3af; margin-top:2px; }
  td.right { text-align:right; }
  .totals { display:flex; justify-content:flex-end; margin-bottom:20px; }
  .totals-box { width:240px; }
  .totals-row { display:flex; justify-content:space-between; padding:5px 0; font-size:10px; border-bottom:1px solid #f3f4f6; }
  .totals-row.total { border-top:2px solid {{ $company->primary_color ?? '#6366f1' }}; border-bottom:none; margin-top:4px; padding-top:8px; }
  .totals-row.total span:first-child { font-weight:800; font-size:13px; }
  .totals-row.total span:last-child { font-weight:800; font-size:14px; color:{{ $company->primary_color ?? '#6366f1' }}; }
  .totals-row.balance { background:{{ $company->primary_color ?? '#6366f1' }}; color:#fff; padding:8px 10px; border-radius:6px; margin-top:6px; font-weight:800; font-size:12px; }
  .payments { margin-bottom:20px; }
  .payments h3 { font-size:8px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:{{ $company->primary_color ?? '#6366f1' }}; margin-bottom:8px; }
  .payment-row { display:flex; justify-content:space-between; padding:5px 0; font-size:9.5px; border-bottom:1px solid #f3f4f6; }
  .terms-box { background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:12px; margin-bottom:16px; }
  .terms-box h3 { font-size:8px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:{{ $company->primary_color ?? '#6366f1' }}; margin-bottom:6px; }
  .terms-box p { font-size:9px; color:#6b7280; line-height:1.7; white-space:pre-wrap; }
  .footer { border-top:1px solid #e5e7eb; padding-top:12px; text-align:center; font-size:9px; color:#9ca3af; }
  .notes-box { margin-bottom:16px; padding:10px 12px; background:#fffbeb; border-left:3px solid #f59e0b; font-size:9.5px; color:#78350f; }
</style>
</head>
<body>
<div class="page">
  {{-- Header --}}
  <div class="header">
    <div class="company-logo">
      @if($company->logo)
        <img src="{{ public_path('storage/'.$company->logo) }}" alt="{{ $company->name }}">
      @else
        <div class="company-name">{{ $company->name }}</div>
      @endif
      <div class="company-details">
        {{ $company->address }}<br>
        @if($company->phone) {{ $company->phone }} · @endif {{ $company->email }}<br>
        @if($company->tax_number) {{ $company->tax_label ?? 'Tax' }}: {{ $company->tax_number }} @endif
      </div>
    </div>
    <div class="doc-title">
      <h1>Invoice</h1>
      <div class="doc-number">{{ $invoice->invoice_number }}</div>
      <div class="doc-meta">Date: {{ $invoice->invoice_date->format('d M Y') }}</div>
      @if($invoice->due_date) <div class="doc-meta">Due: {{ $invoice->due_date->format('d M Y') }}</div> @endif
      <div style="margin-top:6px">
        <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
      </div>
    </div>
  </div>

  {{-- Billing --}}
  <div class="billing">
    <div class="bill-box">
      <h3>Bill To</h3>
      <p>
        <strong>{{ $invoice->customer->name }}</strong><br>
        @if($invoice->customer->organization) {{ $invoice->customer->organization }}<br> @endif
        @if($invoice->customer->address) {{ $invoice->customer->address }}<br> @endif
        @if($invoice->customer->email) {{ $invoice->customer->email }}<br> @endif
        @if($invoice->customer->phone) {{ $invoice->customer->phone }} @endif
      </p>
    </div>
    @if($invoice->project)
    <div class="bill-box" style="text-align:right">
      <h3>Project</h3>
      <p>{{ $invoice->project->name }}</p>
    </div>
    @endif
  </div>

  {{-- Items --}}
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Description</th>
        <th style="width:45px">Qty</th>
        <th style="width:45px">Unit</th>
        <th style="width:75px">Rate</th>
        <th style="width:50px">Tax%</th>
        <th style="width:85px">Amount</th>
      </tr>
    </thead>
    <tbody>
      @foreach($invoice->items as $i => $item)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>
          <div class="item-name">{{ $item->name }}</div>
          @if($item->description) <div class="item-desc">{{ $item->description }}</div> @endif
        </td>
        <td class="right">{{ $item->qty }}</td>
        <td class="right">{{ $item->unit }}</td>
        <td class="right">{{ $company->currency_symbol }}{{ number_format($item->unit_rate, 2) }}</td>
        <td class="right">{{ $item->tax_rate > 0 ? $item->tax_rate.'%' : '—' }}</td>
        <td class="right"><strong>{{ $company->currency_symbol }}{{ number_format($item->amount, 2) }}</strong></td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{-- Totals --}}
  <div class="totals">
    <div class="totals-box">
      <div class="totals-row"><span>Subtotal</span><span>{{ $company->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</span></div>
      @if($invoice->discount_value > 0)
      <div class="totals-row"><span>Discount</span><span>-{{ $company->currency_symbol }}{{ number_format($invoice->discount_amount, 2) }}</span></div>
      @endif
      @if($invoice->tax_rate > 0)
      <div class="totals-row"><span>{{ $invoice->tax_label ?? 'Tax' }} ({{ $invoice->tax_rate }}%)</span><span>{{ $company->currency_symbol }}{{ number_format($invoice->tax_amount, 2) }}</span></div>
      @endif
      <div class="totals-row total"><span>Total</span><span>{{ $company->currency_symbol }}{{ number_format($invoice->total, 2) }}</span></div>
      @if($invoice->paid_amount > 0)
      <div class="totals-row"><span>Paid</span><span style="color:#10b981">{{ $company->currency_symbol }}{{ number_format($invoice->paid_amount, 2) }}</span></div>
      @endif
      <div class="totals-row balance">
        <span>Balance Due</span>
        <span>{{ $company->currency_symbol }}{{ number_format($invoice->balance_due, 2) }}</span>
      </div>
    </div>
  </div>

  {{-- Payment records --}}
  @if($invoice->payments->count())
  <div class="payments">
    <h3>Payment History</h3>
    @foreach($invoice->payments as $pay)
    <div class="payment-row">
      <span>{{ $pay->payment_date->format('d M Y') }} · {{ $pay->paymentType->name ?? 'Payment' }}</span>
      <span>@if($pay->reference_number) Ref: {{ $pay->reference_number }} · @endif <strong>{{ $company->currency_symbol }}{{ number_format($pay->amount, 2) }}</strong></span>
    </div>
    @endforeach
  </div>
  @endif

  {{-- Notes --}}
  @if($invoice->notes)
  <div class="notes-box">{{ $invoice->notes }}</div>
  @endif

  {{-- Terms --}}
  @if($invoice->termsTemplate)
  <div class="terms-box">
    <h3>Terms & Conditions</h3>
    <p>{{ $invoice->termsTemplate->content }}</p>
  </div>
  @endif

  {{-- Footer --}}
  <div class="footer">
    {{ $company->name }} · {{ $company->email }} @if($company->website) · {{ $company->website }} @endif
  </div>
</div>
</body>
</html>
