@extends('layouts.app')
@section('title', 'Quotations Report')
@section('breadcrumb')
  <a href="{{ route('reports.index') }}" style="color:#8b5cf6;text-decoration:none">Reports</a> / Quotations
@endsection

@section('content')
<h1 class="page-title" style="margin-bottom:24px">Quotations Report</h1>

{{-- Funnel stats --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:24px">
  @php
    $funnel = [
      ['label'=>'Total','val'=>$stats['total'],'color'=>'#6366f1'],
      ['label'=>'Sent','val'=>$stats['sent'],'color'=>'#8b5cf6'],
      ['label'=>'Accepted','val'=>$stats['accepted'],'color'=>'#10b981'],
      ['label'=>'Rejected','val'=>$stats['rejected'],'color'=>'#ef4444'],
      ['label'=>'Converted','val'=>$stats['converted'],'color'=>'#f59e0b'],
    ];
  @endphp
  @foreach($funnel as $f)
  <div class="stat-card">
    <div class="stat-value" style="color:{{ $f['color'] }}">{{ $f['val'] }}</div>
    <div class="stat-label">{{ $f['label'] }}</div>
  </div>
  @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
  {{-- Conversion rates --}}
  <div class="card">
    <div class="card-header"><span style="font-weight:700">Conversion Metrics</span></div>
    <div class="card-body">
      @php
        $convRate = $stats['total'] > 0 ? round($stats['converted'] / $stats['total'] * 100, 1) : 0;
        $accRate  = $stats['total'] > 0 ? round($stats['accepted'] / $stats['total'] * 100, 1) : 0;
        $rejRate  = $stats['total'] > 0 ? round($stats['rejected'] / $stats['total'] * 100, 1) : 0;
        $metrics  = [
          ['label'=>'Acceptance Rate','rate'=>$accRate,'color'=>'#10b981'],
          ['label'=>'Rejection Rate','rate'=>$rejRate,'color'=>'#ef4444'],
          ['label'=>'Conversion Rate','rate'=>$convRate,'color'=>'#f59e0b'],
        ];
      @endphp
      @foreach($metrics as $m)
      <div style="margin-bottom:14px">
        <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:4px">
          <span style="color:#6b7280">{{ $m['label'] }}</span>
          <span style="font-weight:800;color:{{ $m['color'] }}">{{ $m['rate'] }}%</span>
        </div>
        <div style="background:#f3f4f6;border-radius:99px;height:10px;overflow:hidden">
          <div style="height:100%;background:{{ $m['color'] }};border-radius:99px;width:{{ $m['rate'] }}%"></div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Value breakdown --}}
  <div class="card">
    <div class="card-header"><span style="font-weight:700">Value</span></div>
    <div class="card-body">
      @php
        $vals = [
          ['label'=>'Total Value','val'=>$stats['total_value'],'color'=>'#6366f1'],
          ['label'=>'Accepted Value','val'=>$stats['accepted_value'],'color'=>'#10b981'],
          ['label'=>'Pending Value','val'=>$stats['pending_value'],'color'=>'#f59e0b'],
        ];
      @endphp
      @foreach($vals as $v)
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6">
        <span style="color:#6b7280;font-size:0.875rem">{{ $v['label'] }}</span>
        <span style="font-weight:800;font-size:0.95rem;color:{{ $v['color'] }}">{{ $company->currency_symbol }}{{ number_format($v['val'] ?? 0, 2) }}</span>
      </div>
      @endforeach
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header"><span style="font-weight:700">All Quotations</span></div>
  <div class="table-wrapper">
    <table class="table">
      <thead><tr><th>Number</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($quotations as $q)
        <tr>
          <td><a href="{{ route('quotations.show', $q) }}" style="font-weight:700;color:#6366f1;text-decoration:none">{{ $q->quotation_number }}</a></td>
          <td>{{ $q->customer->name }}</td>
          <td style="color:#9ca3af;font-size:0.82rem">{{ $q->date->format('d M Y') }}</td>
          <td style="font-weight:700">{{ $company->currency_symbol }}{{ number_format($q->total, 2) }}</td>
          <td><span class="badge badge-{{ $q->status }}">{{ ucfirst($q->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:#9ca3af;padding:24px">No quotations.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
