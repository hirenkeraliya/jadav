@extends('layouts.app')
@section('title', 'Payment Types')
@section('breadcrumb', 'Settings / Payment Types')

@section('content')
<div style="display:flex;gap:20px;max-width:1100px">
  @include('partials.settings-nav')
  <div style="flex:1">
    <h1 class="page-title" style="margin-bottom:24px">Payment Types</h1>

    <div class="card mb-4">
      <div class="card-header"><span style="font-weight:700">Add New Payment Type</span></div>
      <div class="card-body">
        <form method="POST" action="{{ route('settings.payment-types.store') }}" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
          @csrf
          <div>
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" style="min-width:200px" required placeholder="e.g. Bank Transfer">
          </div>
          <button type="submit" class="btn btn-primary">Add</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="table-wrapper">
        <table class="table">
          <thead><tr><th>Name</th><th>Active</th><th style="text-align:right">Actions</th></tr></thead>
          <tbody>
            @forelse($types as $type)
            <tr>
              <td style="font-weight:600">{{ $type->name }}</td>
              <td><span class="badge badge-{{ $type->is_active ? 'active' : 'inactive' }}">{{ $type->is_active ? 'Active' : 'Inactive' }}</span></td>
              <td>
                <div style="display:flex;gap:6px;justify-content:flex-end">
                  <form method="POST" action="{{ route('settings.payment-types.update', $type) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $type->name }}">
                    <input type="hidden" name="is_active" value="{{ $type->is_active ? 0 : 1 }}">
                    <button type="submit" class="btn btn-secondary btn-xs">{{ $type->is_active ? 'Deactivate' : 'Activate' }}</button>
                  </form>
                  <form method="POST" action="{{ route('settings.payment-types.destroy', $type) }}" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:20px">No payment types yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
