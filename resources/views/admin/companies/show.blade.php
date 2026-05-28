@extends('layouts.app')
@section('title', $company->name)
@section('breadcrumb')
  <a href="{{ route('admin.companies.index') }}" style="color:#8b5cf6;text-decoration:none">Companies</a> / {{ $company->name }}
@endsection

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">{{ $company->name }}</h1>
    <span class="badge badge-{{ $company->is_active ? 'active' : 'inactive' }}" style="margin-top:4px">{{ $company->is_active ? 'Active' : 'Inactive' }}</span>
  </div>
  <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-secondary">Edit</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
  <div class="card">
    <div class="card-header"><span style="font-weight:700">Details</span></div>
    <div class="card-body">
      @php $rows = [['Email',$company->email],['Phone',$company->phone],['Website',$company->website],['Address',$company->address],['Currency',$company->currency_symbol.' '.$company->currency],['Tax Label',$company->tax_label],['Tax No',$company->tax_number],['Invoice Prefix',$company->invoice_prefix],['Quotation Prefix',$company->quotation_prefix]]; @endphp
      @foreach($rows as [$label,$val])
        @if($val)
        <div style="display:flex;gap:12px;padding:8px 0;border-bottom:1px solid #f3f4f6;font-size:0.875rem">
          <span style="width:140px;color:#9ca3af;flex-shrink:0">{{ $label }}</span>
          <span style="font-weight:600;color:#1e1b4b;word-break:break-all">{{ $val }}</span>
        </div>
        @endif
      @endforeach
      <div style="padding:8px 0;font-size:0.875rem">
        <span style="width:140px;color:#9ca3af;display:inline-block">Colors</span>
        <span style="display:inline-flex;gap:6px;align-items:center">
          <span style="display:inline-block;width:20px;height:20px;border-radius:5px;background:{{ $company->primary_color ?? '#6366f1' }};border:1px solid rgba(0,0,0,0.08)"></span>
          <span style="font-size:0.78rem;font-family:monospace">{{ $company->primary_color }}</span>
          <span style="display:inline-block;width:20px;height:20px;border-radius:5px;background:{{ $company->secondary_color ?? '#f59e0b' }};border:1px solid rgba(0,0,0,0.08)"></span>
          <span style="font-size:0.78rem;font-family:monospace">{{ $company->secondary_color }}</span>
        </span>
      </div>
    </div>
  </div>

  <div>
    <div class="card mb-4">
      <div class="card-header"><span style="font-weight:700">Users ({{ $company->users->count() }})</span></div>
      @foreach($company->users as $u)
      <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 16px;border-bottom:1px solid #f3f4f6;font-size:0.875rem">
        <div>
          <div style="font-weight:600">{{ $u->name }}</div>
          <div style="color:#9ca3af;font-size:0.78rem">{{ $u->email }}</div>
        </div>
        <div style="display:flex;gap:4px;flex-wrap:wrap">
          @foreach($u->roles as $r)
            <span style="font-size:0.72rem;background:#fef3c7;color:#92400e;padding:2px 7px;border-radius:99px">{{ $r->name }}</span>
          @endforeach
        </div>
      </div>
      @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
      <div class="stat-card">
        <div class="stat-value">{{ $company->projects->count() }}</div>
        <div class="stat-label">Projects</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">{{ $company->customers->count() }}</div>
        <div class="stat-label"Clients</div>
      </div>
    </div>
  </div>
</div>
@endsection
