@extends('layouts.app')
@section('title', 'Project Types')
@section('breadcrumb', 'Settings / Project Types')

@section('content')
<div style="display:flex;gap:20px;max-width:1100px">
  @include('partials.settings-nav')
  <div style="flex:1">
    <h1 class="page-title" style="margin-bottom:24px">Project Types</h1>

    <div class="card mb-4">
      <div class="card-header"><span style="font-weight:700">Add New Type</span></div>
      <div class="card-body">
        <form method="POST" action="{{ route('settings.project-types.store') }}" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
          @csrf
          <div>
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" style="min-width:200px" required>
          </div>
          <div>
            <label class="form-label">Color</label>
            <input type="color" name="color" class="form-control" style="width:48px;height:38px;padding:2px;cursor:pointer" value="#6366f1">
          </div>
          <button type="submit" class="btn btn-primary">Add</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="table-wrapper">
        <table class="table">
          <thead><tr><th>Color</th><th>Name</th><th>Active</th><th style="text-align:right">Actions</th></tr></thead>
          <tbody>
            @forelse($types as $type)
            <tr>
              <td><span style="display:inline-block;width:24px;height:24px;border-radius:6px;background:{{ $type->color }}"></span></td>
              <td style="font-weight:600">{{ $type->name }}</td>
              <td><span class="badge badge-{{ $type->is_active ? 'active' : 'inactive' }}">{{ $type->is_active ? 'Active' : 'Inactive' }}</span></td>
              <td>
                <div style="display:flex;gap:6px;justify-content:flex-end">
                  <form method="POST" action="{{ route('settings.project-types.update', $type) }}" style="display:flex;gap:6px">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $type->name }}">
                    <input type="hidden" name="color" value="{{ $type->color }}">
                    <input type="hidden" name="is_active" value="{{ $type->is_active ? 0 : 1 }}">
                    <button type="submit" class="btn btn-secondary btn-xs">{{ $type->is_active ? 'Deactivate' : 'Activate' }}</button>
                  </form>
                  <form method="POST" action="{{ route('settings.project-types.destroy', $type) }}" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:20px">No types yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
