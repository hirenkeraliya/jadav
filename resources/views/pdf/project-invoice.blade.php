<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
  * { margin:0;padding:0;box-sizing:border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1e1b4b; background:#fff; }
  .page { padding:36px 40px; }
  .header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:28px; padding-bottom:18px; border-bottom:3px solid {{ $company->primary_color ?? '#10b981' }}; }
  .company-name { font-size:20px; font-weight:800; color:{{ $company->primary_color ?? '#10b981' }}; }
  .company-details { font-size:9.5px; color:#6b7280; margin-top:4px; line-height:1.6; }
  .doc-title h1 { font-size:22px; font-weight:800; color:{{ $company->primary_color ?? '#10b981' }}; text-transform:uppercase; letter-spacing:2px; }
  .doc-number { font-size:13px; font-weight:700; color:#1e1b4b; margin-top:4px; }
  .doc-meta { font-size:9.5px; color:#6b7280; margin-top:2px; }
  .billing { display:flex; justify-content:space-between; margin-bottom:24px; }
  .bill-box { width:48%; }
  .bill-box h3 { font-size:8px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:{{ $company->primary_color ?? '#10b981' }}; margin-bottom:7px; }
  .bill-box p { font-size:10px; color:#374151; line-height:1.6; }
  .project-box { background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:10px 12px; margin-bottom:20px; }
  .project-box h3 { font-size:8px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:{{ $company->primary_color ?? '#10b981' }}; margin-bottom:6px; }
  .project-box p { font-size:10px; color:#374151; line-height:1.8; }
  table { width:100%; border-collapse:collapse; margin-bottom:16px; }
  thead tr { background:{{ $company->primary_color ?? '#10b981' }}; }
  thead th { color:#fff; font-size:9px; font-weight:700; text-transform:uppercase; padding:8px 10px; text-align:left; letter-spacing:0.5px; }
  tbody tr:nth-child(even) { background:#f9fafb; }
  tbody td { padding:9px 10px; font-size:10px; border-bottom:1px solid #f3f4f6; }
  td.right { text-align:right; }
  .totals { display:flex; justify-content:flex-end; margin-bottom:20px; }
  .totals-box { width:260px; }
  .totals-row { display:flex; justify-content:space-between; padding:5px 0; font-size:10px; border-bottom:1px solid #f3f4f6; }
  .totals-row.due { border-top:2px solid {{ $company->primary_color ?? '#10b981' }}; border-bottom:none; margin-top:4px; padding-top:8px; font-weight:800; }
  .totals-row.due span:last-child { font-size:14px; color:#ef4444; }
  .payment-section { margin-bottom:20px; }
  .payment-section h3 { font-size:8px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:{{ $company->primary_color ?? '#10b981' }}; margin-bottom:8px; }
  .status-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:1px; }
  .status-unpaid { background:#fee2e2; color:#991b1b; }
  .status-partial { background:#fef3c7; color:#92400e; }
  .status-paid    { background:#d1fae5; color:#065f46; }
  .notes-box { background:#f9fafb; border-left:3px solid {{ $company->primary_color ?? '#10b981' }}; padding:8px 12px; font-size:9.5px; color:#374151; margin-bottom:16px; }
  .terms-box { margin-bottom:16px; }
  .terms-box h3 { font-size:8px; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:{{ $company->primary_color ?? '#10b981' }}; margin-bottom:6px; }
  .terms-box .terms-content { background:#fafaf9; border:1px solid #e5e7eb; border-radius:4px; padding:8px 12px; font-size:9px; color:#4b5563; line-height:1.55; white-space:pre-wrap; }
  .footer { border-top:1px solid #e5e7eb; padding-top:10px; text-align:center; font-size:9px; color:#9ca3af; margin-top:20px; }
</style>
</head>
<body>
<div class="page">

  {{-- Header --}}
  <div class="header">
    <div>
      @if($company->logo)
        <img src="{{ public_path('storage/'.$company->logo) }}" alt="{{ $company->name }}" style="max-height:55px;max-width:160px;margin-bottom:4px">
      @else
        <div class="company-name">{{ $company->name }}</div>
      @endif
      <div class="company-details">
        {{ $company->address }}<br>
        @if($company->phone) {{ $company->phone }} · @endif {{ $company->email }}<br>
        @if($company->tax_number) {{ $company->tax_label ?? 'Tax' }}: {{ $company->tax_number }} @endif
      </div>
    </div>
    <div style="text-align:right">
      <div class="doc-title"><h1>Invoice</h1></div>
      <div class="doc-number">{{ $completion->invoice_number }}</div>
      <div class="doc-meta">Date: {{ $completion->created_at->format('d M Y') }}</div>
      <div style="margin-top:6px">
        <span class="status-badge status-{{ $completion->payment_status }}">{{ ucfirst($completion->payment_status) }}</span>
      </div>
    </div>
  </div>

  {{-- Billing --}}
  <div class="billing">
    <div class="bill-box">
      <h3>Bill To</h3>
      <p>
        <strong>{{ $project->customer->name ?? '—' }}</strong><br>
        @if($project->customer?->organization) {{ $project->customer->organization }}<br> @endif
        @if($project->customer?->address) {{ $project->customer->address }}<br> @endif
        @if($project->customer?->email) {{ $project->customer->email }}<br> @endif
        @if($project->customer?->mobile) {{ $project->customer->mobile }} @endif
      </p>
    </div>
    <div class="bill-box" style="text-align:right">
      <h3>Project</h3>
      <p>
        <strong>{{ $project->name }}</strong><br>
        {{ $project->project_code }}<br>
        @if($project->start_date) {{ $project->start_date->format('d M Y') }} — {{ $project->end_date?->format('d M Y') ?? '—' }} @endif
      </p>
    </div>
  </div>

  {{-- Line items --}}
  <table>
    <thead>
      <tr><th>Description</th><th style="text-align:right">Qty</th><th style="text-align:right">Rate</th><th style="text-align:right">Amount</th></tr>
    </thead>
    <tbody>
      @foreach($completion->items as $item)
      <tr>
        <td>{{ $item->description }}</td>
        <td class="right">{{ number_format($item->qty, 2) }}</td>
        <td class="right">{{ $company->currency_symbol }}{{ number_format($item->rate, 2) }}</td>
        <td class="right">{{ $company->currency_symbol }}{{ number_format($item->amount, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{-- Totals --}}
  <div class="totals">
    <div class="totals-box">
      <div class="totals-row">
        <span>Invoice Total</span>
        <span>{{ $company->currency_symbol }}{{ number_format($completion->total, 2) }}</span>
      </div>
      <div class="totals-row" style="color:#10b981">
        <span>Amount Received</span>
        <span>- {{ $company->currency_symbol }}{{ number_format($completion->paid_amount, 2) }}</span>
      </div>
      <div class="totals-row due">
        <span>Due Amount</span>
        <span>{{ $company->currency_symbol }}{{ number_format($completion->due_amount, 2) }}</span>
      </div>
    </div>
  </div>

  {{-- Payments received --}}
  @if($completion->payments->isNotEmpty())
  <div class="payment-section">
    <h3>Payments Received</h3>
    <table>
      <thead><tr><th>Date</th><th>Reference</th><th>Notes</th><th style="text-align:right">Amount</th></tr></thead>
      <tbody>
        @foreach($completion->payments as $pmt)
        <tr>
          <td>{{ $pmt->date->format('d M Y') }}</td>
          <td>{{ $pmt->reference ?: '—' }}</td>
          <td>{{ $pmt->notes ?: '—' }}</td>
          <td class="right">{{ $company->currency_symbol }}{{ number_format($pmt->amount, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif

  @if($completion->notes)
  <div class="notes-box">{{ $completion->notes }}</div>
  @endif

  @if($completion->termsTemplate)
  <div class="terms-box">
    <h3>Terms &amp; Conditions</h3>
    <div class="terms-content">{{ $completion->termsTemplate->content }}</div>
  </div>
  @endif

  {{-- QR Code + Footer --}}
  @if($company->qr_code)
  <div style="display:flex;justify-content:space-between;align-items:flex-end;border-top:1px solid #e5e7eb;padding-top:12px;margin-top:20px">
    <div style="font-size:9px;color:#9ca3af">
      {{ $company->name }} · {{ $completion->invoice_number }} · Generated {{ now()->format('d M Y') }}
    </div>
    <div style="text-align:center">
      <img src="{{ public_path('storage/'.$company->qr_code) }}" alt="QR Code" style="width:70px;height:70px;object-fit:contain">
      <div style="font-size:8px;color:#9ca3af;margin-top:3px">Scan to Pay</div>
    </div>
  </div>
  @else
  <div class="footer">
    {{ $company->name }} · {{ $completion->invoice_number }} · Generated {{ now()->format('d M Y') }}
  </div>
  @endif

</div>
</body>
</html>
