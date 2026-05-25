@extends('layouts.app')
@section('title', isset($user) ? 'Edit User' : 'New User')
@section('breadcrumb')
  <a href="{{ route('admin.users.index') }}" style="color:#8b5cf6;text-decoration:none">Users</a> / {{ isset($user) ? 'Edit' : 'New' }}
@endsection

@section('content')
<div style="max-width:700px">
  <h1 class="page-title" style="margin-bottom:24px">{{ isset($user) ? 'Edit User' : 'New User' }}</h1>

  <div class="card">
    <div class="card-body">
      <form method="POST"
            action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if(isset($user)) @method('PUT') @endif

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
          </div>
          <div>
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Password {{ isset($user) ? '(leave blank to keep)' : '*' }}</label>
            <input type="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }} autocomplete="new-password">
          </div>
          <div>
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
          </div>
        </div>

        <div style="margin-bottom:16px">
          <label class="form-label">Role</label>
          <select name="role" class="form-control">
            <option value="">— No Role —</option>
            @foreach($roles as $role)
              <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name ?? '') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach
          </select>
        </div>

        <div style="margin-bottom:16px">
          <label class="form-label">Companies</label>
          <div style="display:flex;flex-direction:column;gap:6px;padding:10px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb">
            @foreach($companies as $co)
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:0.875rem">
              <input type="checkbox" name="companies[]" value="{{ $co->id }}"
                     {{ in_array($co->id, old('companies', isset($user) ? $user->companies->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
              {{ $co->name }}
            </label>
            @endforeach
          </div>
        </div>

        <div style="display:flex;gap:16px;margin-bottom:20px">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:0.875rem;font-weight:500">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
            Active
          </label>
          @if(auth()->user()->is_super_admin)
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:0.875rem;font-weight:500">
            <input type="checkbox" name="is_super_admin" value="1" {{ old('is_super_admin', $user->is_super_admin ?? false) ? 'checked' : '' }}>
            Super Admin
          </label>
          @endif
        </div>

        <div style="display:flex;gap:10px">
          <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Update User' : 'Create User' }}</button>
          <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
