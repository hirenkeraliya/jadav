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
    @if(in_array($quotation->status, ['draft','sent']))
      <form method="POST" action="{{ route('quotations.revise', $quotation) }}">
        @csrf
        <button type="submit" class="btn btn-secondary">Revise</button>
      </form>
      <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-secondary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
      </a>
    @endif
    @if($quotation->status !== 'converted' && !$quotation->project)
      <button type="button" onclick="document.getElementById('convertModal').style.display='flex'" class="btn btn-success">
        Convert to Project
      </button>
    @endif
    <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" onsubmit="return confirm('Delete this quotation?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger">Delete</button>
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

@if($quotation->status !== 'converted' && !$quotation->project)
{{-- Convert to Project modal --}}
@php $customer = $quotation->customer; $needsDetails = !$customer->email && !$customer->mobile && !$customer->organization; @endphp
<div id="convertModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:16px;width:680px;max-width:96vw;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,0.25)">

    {{-- Modal header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 28px;border-bottom:1px solid #f3f4f6;flex-shrink:0">
      <div>
        <h3 style="font-size:1.05rem;font-weight:800;color:#1e1b4b;margin:0">Convert to Project</h3>
        <div style="font-size:0.8rem;color:#9ca3af;margin-top:2px">Quotation {{ $quotation->quotation_number }} · {{ $customer->name }}</div>
      </div>
      <button type="button" onclick="document.getElementById('convertModal').style.display='none'"
              style="background:none;border:none;cursor:pointer;font-size:1.4rem;color:#9ca3af;line-height:1">&times;</button>
    </div>

    {{-- Scrollable body --}}
    <div style="overflow-y:auto;flex:1;padding:24px 28px">
      <form id="convertForm" method="POST" action="{{ route('quotations.convert', $quotation) }}">
        @csrf

        {{-- Section: Project Info --}}
        <div style="font-size:0.68rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#8b5cf6;margin-bottom:12px;padding-bottom:6px;border-bottom:2px solid #ede9fe">
          Project Information
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
          <div>
            <label class="form-label">Project Code <span style="color:#ef4444">*</span></label>
            <input type="text" name="project_code" class="form-control" required
                   value="{{ old('project_code', $suggestedCode) }}" placeholder="e.g. PRJ-2026-0001">
          </div>
          <div>
            <label class="form-label">Project Name <span style="color:#ef4444">*</span></label>
            <input type="text" name="project_name" class="form-control" required
                   value="{{ old('project_name', $customer->name . ' Project') }}">
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px">
          <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              @foreach(['running','on_hold','quotation'] as $s)
                <option value="{{ $s }}" {{ old('status','running') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="form-label">Priority</label>
            <select name="priority" class="form-control">
              @foreach(['low','medium','high'] as $p)
                <option value="{{ $p }}" {{ old('priority','medium') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="form-label">Lead By</label>
            <select name="lead_by" class="form-control">
              <option value="">— None —</option>
              @foreach($users as $u)
                <option value="{{ $u->id }}" {{ old('lead_by') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
          <div>
            <label class="form-label">Start Date <span style="color:#ef4444">*</span></label>
            <input type="date" name="start_date" class="form-control" required value="{{ old('start_date') }}">
          </div>
          <div>
            <label class="form-label">End Date <span style="color:#ef4444">*</span></label>
            <input type="date" name="end_date" class="form-control" required value="{{ old('end_date') }}">
          </div>
        </div>

        <div style="margin-bottom:14px">
          <label class="form-label">Location / City</label>
          <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="City or site location">
        </div>

        {{-- Project Types --}}
        @if($projectTypes->isNotEmpty())
        <div style="margin-bottom:14px">
          <label class="form-label">Project Types <span style="color:#ef4444">*</span></label>
          <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px">
            @foreach($projectTypes as $pt)
            <label style="display:inline-flex;align-items:center;gap:7px;cursor:pointer;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:5px 12px;font-size:0.85rem"
                   onclick="this.style.background=this.querySelector('input').checked?'#f9fafb':'#ede9fe';this.style.borderColor=this.querySelector('input').checked?'#e5e7eb':'#8b5cf6'">
              <input type="checkbox" name="project_type_ids[]" value="{{ $pt->id }}"
                     style="accent-color:#8b5cf6;width:14px;height:14px"
                     {{ in_array($pt->id, (array) old('project_type_ids', [])) ? 'checked' : '' }}>
              <span style="width:9px;height:9px;border-radius:50%;background:{{ $pt->color }};flex-shrink:0"></span>
              {{ $pt->name }}
            </label>
            @endforeach
          </div>
        </div>
        @endif

        <div style="margin-bottom:14px">
          <label class="form-label">Scope of Work</label>
          <textarea name="scope_of_work" class="form-control" rows="3"
                    placeholder="Describe what this project covers…">{{ old('scope_of_work') }}</textarea>
        </div>

        <div style="margin-bottom:20px">
          <label class="form-label">Internal Notes</label>
          <textarea name="internal_notes" class="form-control" rows="2"
                    placeholder="Private notes visible to your team only…">{{ old('internal_notes') }}</textarea>
        </div>

        {{-- Customer details (if incomplete) --}}
        @if($needsDetails)
        <div style="font-size:0.68rem;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:#8b5cf6;margin-bottom:12px;padding-bottom:6px;border-bottom:2px solid #ede9fe">
          Customer Details
        </div>
        <div style="background:#fef9c3;border:1px solid #fde047;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:0.82rem;color:#713f12">
          <strong>{{ $customer->name }}</strong> has no contact details on file. Please add them now (optional).
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px">
          <div>
            <label class="form-label">Organization</label>
            <input type="text" name="customer_organization" class="form-control" value="{{ old('customer_organization', $customer->organization) }}" placeholder="Company name">
          </div>
          <div>
            <label class="form-label">Email</label>
            <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', $customer->email) }}" placeholder="email@example.com">
          </div>
          <div>
            <label class="form-label">Mobile</label>
            <input type="text" name="customer_mobile" class="form-control" value="{{ old('customer_mobile', $customer->mobile) }}" placeholder="+91 …">
          </div>
          <div>
            <label class="form-label">Address</label>
            <input type="text" name="customer_address" class="form-control" value="{{ old('customer_address', $customer->address) }}" placeholder="Address">
          </div>
        </div>
        @endif

      </form>
    </div>

    {{-- Modal footer --}}
    <div style="display:flex;gap:10px;justify-content:flex-end;padding:16px 28px;border-top:1px solid #f3f4f6;flex-shrink:0">
      <button type="button" onclick="document.getElementById('convertModal').style.display='none'"
              style="padding:9px 20px;border:1px solid #d1d5db;border-radius:6px;background:#fff;cursor:pointer;font-size:0.875rem">
        Cancel
      </button>
      <button type="submit" form="convertForm" class="btn btn-success" style="padding:9px 24px">
        Create Project
      </button>
    </div>

  </div>
</div>
@endif

@endsection
