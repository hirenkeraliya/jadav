@extends('layouts.app')
@section('title', 'Reports')
@section('breadcrumb', 'Reports')

@section('content')
<h1 class="page-title" style="margin-bottom:24px">Reports</h1>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px">
  @php
    $reports = [
      ['route'=>'reports.finance','icon'=>'M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6','title'=>'Finance Report','desc'=>'Credit vs debit breakdown by project, with date range filter.'],
      ['route'=>'reports.quotations','icon'=>'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z','title'=>'Quotations Report','desc'=>'Quotation funnel: acceptance rate, conversion stats.'],
      ['route'=>'reports.projects','icon'=>'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z','title'=>'Projects Report','desc'=>'Project status breakdown and performance overview.'],
    ];
  @endphp
  @foreach($reports as $r)
  <a href="{{ route($r['route']) }}" style="text-decoration:none">
    <div class="card" style="cursor:pointer;transition:all .15s ease;border:2px solid transparent" onmouseover="this.style.borderColor='#8b5cf6';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='transparent';this.style.transform=''">
      <div class="card-body" style="display:flex;flex-direction:column;gap:12px;padding:22px">
        <div style="width:44px;height:44px;background:linear-gradient(135deg,#ede9fe,#c4b5fd);border-radius:12px;display:flex;align-items:center;justify-content:center">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2"><path d="{{ $r['icon'] }}"/></svg>
        </div>
        <div>
          <div style="font-weight:800;font-size:1rem;color:#1e1b4b;margin-bottom:4px">{{ $r['title'] }}</div>
          <div style="font-size:0.82rem;color:#9ca3af;line-height:1.5">{{ $r['desc'] }}</div>
        </div>
        <div style="font-size:0.8rem;color:#8b5cf6;font-weight:600">View Report →</div>
      </div>
    </div>
  </a>
  @endforeach
</div>
@endsection
