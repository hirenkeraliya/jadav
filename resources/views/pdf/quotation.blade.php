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
  .company-name { font-size:20px; font-weight:800; color:{{ $company->primary_color ?? '#6366f1' }}; }
  .company-details { font-size:9.5px; color:#6b7280; margin-top:4px; line-height:1.6; }
  .doc-title h1 { font-size:24px; font-weight:800; color:{{ $company->primary_color ?? '#6366f1' }}; text-transform:uppercase; letter-spacing:2px; }
  .doc-number { font-size:13px; font-weight:700; color:#1e1b4b; margin-top:4px; }
  .doc-meta { font-size:9.5px; color:#6b7280; margin-top:2px; }
  .status-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:1px; }
  .status-draft { background:#f3f4f6; color:#6b7280; }
  .status-sent { background:#ede9fe; color:#6366f1; }
  .status-accepted { background:#d1fae5; color:#065f46; }
  .status-rejected { background:#fee2e2; color:#991b1b; }
  .status-converted { background:#fef3c7; color:#92400e; }
  .billing { display:flex; justify-content:space-between; margin-bottom:24px; }
  .bill-box { width:48%; }
  .bill-box h3 { font-size:8px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:{{ $company->primary_color ?? '#6366f1' }}; margin-bottom:7px; }
  .bill-box p { font-size:10px; color:#374151; line-height:1.6; }
  table { width:100%; border-collapse:collapse; margin-bottom:16px; }
  thead tr { background:{{ $company->primary_color ?? '#6366f1' }}; }
  thead th { color:#fff; font-size:9px; font-weight:700; text-transform:uppercase; padding:8px 10px; text-align:left; letter-spacing:0.5px; }
  tbody tr:nth-child(even) { background:#f9fafb; }
  tbody td { padding:9px 10px; font-size:10px; border-bottom:1px solid #f3f4f6; vertical-align:top; }
  .item-name { font-weight:700; color:#1e1b4b; }
  .item-desc { font-size:8.5px; color:#9ca3af; margin-top:2px; }
  td.right { text-align:right; }
  .totals { display:flex; justify-content:flex-end; margin-bottom:20px; }
  .totals-box { width:240px; }
  .totals-row { display:flex; justify-content:space-between; padding:5px 0; font-size:10px; border-bottom:1px solid #f3f4f6; }
  .totals-row.total { border-top:2px solid {{ $company->primary_color ?? '#6366f1' }}; border-bottom:none; margin-top:4px; padding-top:8px; font-weight:800; }
  .totals-row.total span:last-child { font-size:14px; color:{{ $company->primary_color ?? '#6366f1' }}; }
  .valid-box { background:#f0fdf4; border:1px solid #bbf7d0; padding:8px 12px; border-radius:6px; margin-bottom:16px; font-size:9.5px; color:#065f46; }
  .revision-box { background:#f5f3ff; border:1px solid #c4b5fd; padding:8px 12px; border-radius:6px; margin-bottom:16px; font-size:9.5px; color:#4c1d95; }
  .terms-box { background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:12px; margin-bottom:16px; }
  .terms-box h3 { font-size:8px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:{{ $company->primary_color ?? '#6366f1' }}; margin-bottom:6px; }
  .terms-box p { font-size:9px; color:#6b7280; line-height:1.7; white-space:pre-wrap; }
  .footer { border-top:1px solid #e5e7eb; padding-top:12px; text-align:center; font-size:9px; color:#9ca3af; }
  .notes-box { margin-bottom:16px; padding:10px 12px; background:#fffbeb; border-left:3px solid #f59e0b; font-size:9.5px; color:#78350f; }
  .signature-area { display:flex; justify-content:space-between; margin-top:30px; margin-bottom:20px; }
  .signature-box { width:200px; border-top:1px solid #e5e7eb; padding-top:6px; font-size:9px; color:#9ca3af; text-align:center; }
</style>
</head>
<body>
<div class="page">
  {{-- Header --}}
  <div class="header">
    <div>
      @if($company->logo)
        <img src="{{ public_path('storage/'.$company->logo) }}" alt="{{ $company->name }}" style="max-height:60px;max-width:180px;margin-bottom:4px">
      @else
        <div class="company-name">{{ $company->name }}</div>
      @endif
      <div class="company-details">
        {{ $company->address }}<br>
        @if($company->phone) {{ $company->phone }} · @endif {{ $company->email }}<br>
        @if($company->tax_number) {{ $company->tax_label ?? 'Tax' }}: {{ $company->tax_number }} @endif
      </div>
    </div>
    <div class="doc-title" style="text-align:right">
      <h1>Quotation</h1>
      <div class="doc-number">{{ $quotation->quotation_number }}</div>
      @if($quotation->revision > 0) <div class="doc-meta">Revision: {{ $quotation->revision }}</div> @endif
      <div class="doc-meta">Date: {{ $quotation->quotation_date->format('d M Y') }}</div>
      @if($quotation->valid_until) <div class="doc-meta">Valid Until: {{ $quotation->valid_until->format('d M Y') }}</div> @endif
      <div style="margin-top:6px">
        <span class="status-badge status-{{ $quotation->status }}">{{ ucfirst($quotation->status) }}</span>
      </div>
    </div>
  </div>

  {{-- Expiry warning --}}
  @if($quotation->valid_until)
  <div class="valid-box">
    This quotation is valid until <strong>{{ $quotation->valid_until->format('d F Y') }}</strong>.
    @if($quotation->valid_until->isPast()) This quotation has expired. @endif
  </div>
  @endif

  {{-- Billing --}}
  <div class="billing">
    <div class="bill-box">
      <h3>Prepared For</h3>
      <p>
        <strong>{{ $quotation->customer->name }}</strong><br>
        @if($quotation->customer->organization) {{ $quotation->customer->organization }}<br> @endif
        @if($quotation->customer->address) {{ $quotation->customer->address }}<br> @endif
        @if($quotation->customer->email) {{ $quotation->customer->email }}<br> @endif
        @if($quotation->customer->phone) {{ $quotation->customer->phone }} @endif
      </p>
    </div>
    @if($quotation->project)
    <div class="bill-box" style="text-align:right">
      <h3>Project</h3>
      <p>{{ $quotation->project->name }}</p>
    </div>
    @endif
  </div>

  {{-- Items --}}
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Description</th>
        <th style="width:45px;text-align:right">Qty</th>
        <th style="width:45px;text-align:right">Unit</th>
        <th style="width:75px;text-align:right">Rate</th>
        <th style="width:50px;text-align:right">Tax%</th>
        <th style="width:85px;text-align:right">Amount</th>
      </tr>
    </thead>
    <tbody>
      @foreach($quotation->items as $i => $item)
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
      <div class="totals-row"><span>Subtotal</span><span>{{ $company->currency_symbol }}{{ number_format($quotation->subtotal, 2) }}</span></div>
      @if($quotation->discount_value > 0)
      <div class="totals-row"><span>Discount</span><span>-{{ $company->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span></div>
      @endif
      @if($quotation->tax_rate > 0)
      <div class="totals-row"><span>{{ $quotation->tax_label ?? 'Tax' }} ({{ $quotation->tax_rate }}%)</span><span>{{ $company->currency_symbol }}{{ number_format($quotation->tax_amount, 2) }}</span></div>
      @endif
      <div class="totals-row total"><span>Total</span><span>{{ $company->currency_symbol }}{{ number_format($quotation->total, 2) }}</span></div>
    </div>
  </div>

  {{-- Notes --}}
  @if($quotation->notes)
  <div class="notes-box">{{ $quotation->notes }}</div>
  @endif

  {{-- Terms --}}
  @if($quotation->termsTemplate)
  <div class="terms-box">
    <h3>Terms & Conditions</h3>
    <p>{{ $quotation->termsTemplate->content }}</p>
  </div>
  @endif

  {{-- Signature --}}
  <div class="signature-area">
    <div class="signature-box">Authorized Signature<br><br><br>{{ $company->name }}</div>
    <div class="signature-box">Client Acceptance<br><br><br>{{ $quotation->customer->name }}</div>
  </div>

  {{-- Footer --}}
  <div class="footer">
    {{ $company->name }} · {{ $company->email }} @if($company->website) · {{ $company->website }} @endif
  </div>
</div>
</body>
</html>
