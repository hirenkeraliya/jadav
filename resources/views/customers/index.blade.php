@extends('layouts.app')
@section('title', 'Customers')
@section('breadcrumb', 'Customers')

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Customers</h1>
    <p class="page-subtitle">{{ $customers->total() }} total clients</p>
  </div>
  <a href="{{ route('customers.create') }}" class="btn btn-primary">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Add Customer
  </a>
</div>

{{-- Filters --}}
<div class="card mb-5">
  <div class="card-body" style="padding:16px 20px">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
      <input type="text" name="search" class="form-control" style="max-width:260px"
             placeholder="Search name, email, org..." value="{{ request('search') }}">
      <select name="status" class="form-control" style="max-width:150px">
        <option value="">All statuses</option>
        <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
        <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      @if(request('search') || request('status'))
        <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">Clear</a>
      @endif
    </form>
  </div>
</div>

<div class="card">
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Contact</th>
          <th>Organization</th>
          <th>Status</th>
          <th>Projects</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($customers as $customer)
        <tr>
          <td>
            @if($customer->customer_code)
              <span style="display:inline-block;background:#ede9fe;color:#6d28d9;font-size:0.95rem;font-weight:800;letter-spacing:0.05em;padding:3px 10px;border-radius:6px;margin-bottom:5px">{{ $customer->customer_code }}</span>
            @endif
            <div><a href="{{ route('customers.show', $customer) }}" style="font-weight:600;color:#4f46e5;text-decoration:none">{{ $customer->name }}</a></div>
            @if($customer->source)
              <div style="font-size:0.72rem;color:#9ca3af;margin-top:1px">Source: {{ $customer->source }}</div>
            @endif
          </td>
          <td>
            <div>{{ $customer->email }}</div>
            <div style="font-size:0.8rem;color:#9ca3af">{{ $customer->mobile }}</div>
          </td>
          <td>{{ $customer->organization ?? '—' }}</td>
          <td><span class="badge badge-{{ $customer->status }}">{{ ucfirst($customer->status) }}</span></td>
          <td>{{ $customer->projects_count ?? 0 }}</td>
          <td>
            <div style="display:flex;gap:6px;justify-content:flex-end">
              <a href="{{ route('customers.show', $customer) }}" class="btn btn-secondary btn-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> View
              </a>
              <a href="{{ route('customers.edit', $customer) }}" class="btn btn-secondary btn-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
              </a>
              <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                    onsubmit="return confirm('Delete this customer?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-xs">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg> Delete
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align:center;padding:48px;color:#9ca3af">
            No customers found.
            <a href="{{ route('customers.create') }}" style="color:#6366f1;font-weight:600">Add the first one →</a>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($customers->hasPages())
  <div style="padding:16px 20px;border-top:1px solid #f3f4f6">
    {{ $customers->links() }}
  </div>
  @endif
</div>
@endsection
