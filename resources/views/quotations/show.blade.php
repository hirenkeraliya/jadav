@extends('layouts.app')
@section('title', $quotation->quotation_number)
@section('breadcrumb')
  <a href="{{ route('quotations.index') }}" style="color:#8b5cf6;text-decoration:none">Quotations</a> /
  {{ $quotation->quotation_number }}
@endsection

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">{{ $quotation->quotation_number }}</h1>
    <div style="display:flex;gap:8px;margin-top:6px;flex-wrap:wrap">
      <span class="badge badge-{{ $quotation->status }}">{{ ucfirst($quotation->status) }}</span>
      @if($quotation->version > 1) <span class="badge badge-draft">Revision v{{ $quotation->version }}</span> @endif
    </div>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <a href="{{ route('quotations.pdf', $quotation) }}" target="_blank" class="btn btn-secondary">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> PDF
    </a>
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      PDF
    </a>
    @if(in_array($quotation->status, ['draft','sent']))
      <form method="POST" action="{{ route('quotations.revise', $quotation) }}">
        @csrf
        <button type="submit" class="btn btn-secondary">Revise</button>
      </form>
      <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
      </a>
    @endif
    @if($quotation->status === 'accepted')
      <button type="button" onclick="document.getElementById('convertModal').style.display='flex'" class="btn btn-success">
        Convert to Project
      </button>
    @endif
    <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" onsubmit="return confirm('Delete this quotation?')">
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
            <div style="font-size:0.75rem;font-weight:700;color:#8b5cf6;margin-bottom:4px">Customer</div>
            <div style="font-weight:700;font-size:1.05rem;color:#1e1b4b">{{ $quotation->customer->name }}</div>
            @if($quotation->customer->organization) <div style="font-size:0.85rem;color:#6b7280">{{ $quotation->customer->organization }}</div> @endif
            @if($quotation->customer->email) <div style="font-size:0.85rem;color:#6b7280">{{ $quotation->customer->email }}</div> @endif
          </div>
          <div style="text-align:right">
            <div style="font-size:0.75rem;font-weight:700;color:#8b5cf6;margin-bottom:4px">From</div>
            <div style="font-weight:700;color:#1e1b4b">{{ $company->name }}</div>
            <div style="font-size:0.85rem;color:#6b7280">{{ $company->email }}</div>
          </div>
        </div>

        <div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:20px">
          <div><div style="font-size:0.72rem;font-weight:700;color:#8b5cf6">Date</div><div>{{ $quotation->date->format('d M Y') }}</div></div>
          @if($quotation->valid_until)
          <div><div style="font-size:0.72rem;font-weight:700;color:#8b5cf6">Valid Until</div><div>{{ $quotation->valid_until->format('d M Y') }}</div></div>
          @endif
        </div>

        {{-- Items --}}
        <table style="width:100%;border-collapse:collapse;font-size:0.875rem;margin-bottom:16px">
          <thead>
            <tr style="border-bottom:2px solid #ede9fe">
              <th style="text-align:left;padding:8px 0;color:#8b5cf6;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em">#</th>
              <th style="text-align:left;padding:8px;color:#8b5cf6;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em">Description</th>
              <th style="text-align:right;padding:8px;color:#8b5cf6;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em">Qty</th>
              <th style="text-align:right;padding:8px;color:#8b5cf6;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em">Unit</th>
              <th style="text-align:right;padding:8px;color:#8b5cf6;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em">Rate</th>
              <th style="text-align:right;padding:8px 0;color:#8b5cf6;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em">Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($quotation->items as $i => $item)
            <tr style="border-bottom:1px solid #f3f4f6">
              <td style="padding:10px 0;color:#9ca3af">{{ $i + 1 }}</td>
              <td style="padding:10px 8px">
                <div style="font-weight:600;color:#1e1b4b">{{ $item->name }}</div>
                @if($item->description) <div style="font-size:0.8rem;color:#9ca3af">{{ $item->description }}</div> @endif
              </td>
              <td style="text-align:right;padding:10px 8px">{{ $item->qty }}</td>
              <td style="text-align:right;padding:10px 8px;color:#9ca3af">{{ $item->unit }}</td>
              <td style="text-align:right;padding:10px 8px">{{ $company->currency_symbol }}{{ number_format($item->unit_rate, 2) }}</td>
              <td style="text-align:right;padding:10px 0;font-weight:600">{{ $company->currency_symbol }}{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>

        {{-- Subtotals --}}
        <div style="display:flex;justify-content:flex-end">
          <div style="width:260px">
            <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:0.875rem">
              <span style="color:#6b7280">Subtotal</span>
              <span>{{ $company->currency_symbol }}{{ number_format($quotation->subtotal, 2) }}</span>
            </div>
            @if($quotation->discount_value > 0)
            <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:0.875rem">
              <span style="color:#6b7280">Discount{{ $quotation->discount_type === 'percentage' ? ' ('.$quotation->discount_value.'%)' : '' }}</span>
              <span style="color:#ef4444">-{{ $company->currency_symbol }}{{ number_format($quotation->discount_amount, 2) }}</span>
            </div>
            @endif
            @if($quotation->tax_rate > 0)
            <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:0.875rem">
              <span style="color:#6b7280">{{ $quotation->tax_label ?? 'Tax' }} ({{ $quotation->tax_rate }}%)</span>
              <span>{{ $company->currency_symbol }}{{ number_format($quotation->tax_amount, 2) }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;padding:10px 0;border-top:2px solid #ede9fe;font-size:1.1rem;font-weight:800;color:#1e1b4b">
              <span>Total</span>
              <span style="color:#6366f1">{{ $company->currency_symbol }}{{ number_format($quotation->total, 2) }}</span>
            </div>
          </div>
        </div>

        @if($quotation->notes)
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6">
          <div style="font-size:0.75rem;font-weight:700;color:#8b5cf6;margin-bottom:6px">Notes</div>
          <div style="font-size:0.875rem;color:#6b7280;white-space:pre-wrap">{{ $quotation->notes }}</div>
        </div>
        @endif

        @if($quotation->termsTemplate)
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6">
          <div style="font-size:0.75rem;font-weight:700;color:#8b5cf6;margin-bottom:6px">Terms & Conditions</div>
          <div style="font-size:0.8rem;color:#6b7280;white-space:pre-wrap">{{ $quotation->termsTemplate->content }}</div>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Sidebar --}}
  <div>
    @if($quotation->revisions->isNotEmpty() || $quotation->parent_id)
    <div class="card mb-4">
      <div class="card-header"><span style="font-weight:700">Revisions</span></div>
      <div style="padding:8px 12px">
        @foreach($quotation->revisions as $rev)
        <a href="{{ route('quotations.show', $rev) }}"
           style="display:block;padding:10px 10px;border-radius:8px;text-decoration:none;margin-bottom:4px;{{ $rev->id === $quotation->id ? 'background:#f5f3ff;' : 'background:#fafafa;border:1px solid #f3f4f6;' }}">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
            <span style="font-size:0.85rem;font-weight:700;color:#4f46e5">{{ $rev->quotation_number }}</span>
            <span class="badge badge-{{ $rev->status }}" style="font-size:0.65rem">{{ ucfirst($rev->status) }}</span>
          </div>
          <div style="font-size:0.75rem;color:#9ca3af">
            @if($rev->revisor)
              <span style="color:#6b7280">By {{ $rev->revisor->name }}</span> &middot;
            @endif
            {{ $rev->updated_at->format('d M Y, h:i A') }}
          </div>
        </a>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Current quotation edit info --}}
    @if($quotation->revisor && !$quotation->revisions->isNotEmpty())
    <div class="card mb-4">
      <div class="card-body" style="font-size:0.82rem;color:#6b7280">
        <div style="font-size:0.72rem;font-weight:700;color:#8b5cf6;margin-bottom:4px">Last Edited</div>
        <div>{{ $quotation->revisor->name }}</div>
        <div style="font-size:0.75rem;color:#9ca3af">{{ $quotation->updated_at->format('d M Y, h:i A') }}</div>
      </div>
    </div>
    @endif

    @if($quotation->project)
    <div class="card mb-4">
      <div class="card-body">
        <div style="font-size:0.75rem;font-weight:700;color:#8b5cf6;margin-bottom:8px">Linked Project</div>
        <a href="{{ route('projects.show', $quotation->project) }}" style="font-weight:700;color:#4f46e5;text-decoration:none">
          {{ $quotation->project->name }}
        </a>
      </div>
    </div>
    @endif
  </div>
</div>

@if($quotation->status === 'accepted')
{{-- Convert to Project modal --}}
@php $customer = $quotation->customer; $needsDetails = !$customer->email && !$customer->mobile && !$customer->organization; @endphp
<div id="convertModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:16px;padding:28px;width:500px;max-width:96vw;box-shadow:0 20px 60px rgba(0,0,0,0.2)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <h3 style="font-size:1.05rem;font-weight:800;color:#1e1b4b">Convert to Project</h3>
      <button type="button" onclick="document.getElementById('convertModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.3rem;color:#9ca3af">&times;</button>
    </div>
    <form method="POST" action="{{ route('quotations.convert', $quotation) }}">
      @csrf
      <div style="margin-bottom:14px">
        <label class="form-label">Project Name</label>
        <input type="text" name="project_name" class="form-control"
               placeholder="{{ $customer->name }} Project"
               value="{{ old('project_name') }}">
        <span class="form-hint">Leave blank to use customer name</span>
      </div>

      @if($needsDetails)
      <div style="background:#fef9c3;border:1px solid #fde047;border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:0.83rem;color:#713f12">
        <strong>{{ $customer->name }}</strong> has no contact details on file. Please add them now (optional but recommended).
      </div>
      <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:#8b5cf6;margin-bottom:10px">Customer Details</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
        <div>
          <label class="form-label">Organization</label>
          <input type="text" name="customer_organization" class="form-control" placeholder="Company name" value="{{ old('customer_organization', $customer->organization) }}">
        </div>
        <div>
          <label class="form-label">Email</label>
          <input type="email" name="customer_email" class="form-control" placeholder="email@example.com" value="{{ old('customer_email', $customer->email) }}">
        </div>
        <div>
          <label class="form-label">Mobile</label>
          <input type="text" name="customer_mobile" class="form-control" placeholder="+91 …" value="{{ old('customer_mobile', $customer->mobile) }}">
        </div>
        <div>
          <label class="form-label">Address</label>
          <input type="text" name="customer_address" class="form-control" placeholder="Address" value="{{ old('customer_address', $customer->address) }}">
        </div>
      </div>
      @endif

      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button type="button" onclick="document.getElementById('convertModal').style.display='none'"
                style="padding:9px 18px;border:1px solid #d1d5db;border-radius:6px;background:#fff;cursor:pointer;font-size:0.875rem">
          Cancel
        </button>
        <button type="submit" class="btn btn-success">Create Project</button>
      </div>
    </form>
  </div>
</div>
@endif

@endsection
