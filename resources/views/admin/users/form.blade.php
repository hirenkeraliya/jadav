@extends('layouts.app')
@section('title', isset($user) ? 'Edit User' : 'New User')
@section('breadcrumb')
  <a href="{{ route('admin.users.index') }}" style="color:var(--color-primary);text-decoration:none;font-weight:600">Users</a>
  <span style="color:#d1d5db;margin:0 6px">/</span>
  {{ isset($user) ? 'Edit' : 'New User' }}
@endsection

@push('styles')
<style>
  .form-page-header {
    display: flex; align-items: center; gap: 16px; margin-bottom: 28px;
  }
  .form-page-icon {
    width: 48px; height: 48px; border-radius: 14px;
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    box-shadow: 0 4px 14px color-mix(in srgb, var(--color-primary) 35%, transparent);
    flex-shrink: 0;
  }
  .form-page-title   { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; color: var(--color-primary-text-dark); margin: 0; }
  .form-page-subtitle{ font-size: 0.85rem; color: #9ca3af; margin: 3px 0 0; }
  .form-section {
    padding: 24px;
    border-bottom: 1px solid #f3f4f6;
  }
  .form-section:last-of-type { border-bottom: none; }
  .form-section-title {
    font-size: 0.78rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.09em; color: var(--color-primary);
    margin-bottom: 18px; display: flex; align-items: center; gap: 8px;
  }
  .form-section-title::after {
    content: ''; flex: 1; height: 1px;
    background: var(--color-primary-border);
  }
  .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  @media (max-width: 640px) { .form-grid-2 { grid-template-columns: 1fr; } }

  .toggle-wrap {
    display: flex; flex-direction: column; gap: 10px;
  }
  .toggle-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 16px; border-radius: 12px;
    border: 1.5px solid #e5e7eb; background: #fafaf9;
    cursor: pointer; transition: all 0.15s;
  }
  .toggle-row:hover { border-color: var(--color-primary); background: var(--color-primary-subtle); }
  .toggle-row label { cursor: pointer; }
  .toggle-info-name  { font-size: 0.875rem; font-weight: 600; color: #111827; }
  .toggle-info-desc  { font-size: 0.75rem; color: #9ca3af; margin-top: 1px; }
  /* nice toggle switch */
  .toggle-switch {
    position: relative; width: 42px; height: 24px; flex-shrink: 0;
  }
  .toggle-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
  .toggle-slider {
    position: absolute; inset: 0;
    background: #e5e7eb; border-radius: 12px;
    transition: background 0.2s;
    cursor: pointer;
  }
  .toggle-slider::before {
    content: ''; position: absolute;
    width: 18px; height: 18px;
    left: 3px; top: 3px;
    background: #fff; border-radius: 50%;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
  }
  .toggle-switch input:checked + .toggle-slider { background: var(--color-primary); }
  .toggle-switch input:checked + .toggle-slider::before { transform: translateX(18px); }

  .company-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 8px;
  }
  .company-check {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: 10px;
    border: 1.5px solid #e5e7eb; cursor: pointer;
    transition: all 0.15s; background: #fafaf9; font-size: 0.875rem; font-weight: 500;
  }
  .company-check:hover         { border-color: var(--color-primary); background: var(--color-primary-subtle); }
  .company-check input:checked ~ * { color: var(--color-primary); }
  .company-check:has(input:checked) { border-color: var(--color-primary); background: var(--color-primary-subtle); }

  .form-footer {
    padding: 20px 24px;
    border-top: 1px solid #f3f4f6;
    display: flex; align-items: center; gap: 10px;
    background: #fafaf9; border-radius: 0 0 16px 16px;
  }
</style>
@endpush

@section('content')
<div style="max-width:760px">

  {{-- Page header --}}
  <div class="form-page-header">
    <div class="form-page-icon">
      @if(isset($user))
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      @else
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
      @endif
    </div>
    <div>
      <h1 class="form-page-title">{{ isset($user) ? 'Edit User' : 'Create New User' }}</h1>
      <p class="form-page-subtitle">{{ isset($user) ? 'Update ' . $user->name . '\'s account details' : 'Add a new user to the platform' }}</p>
    </div>
  </div>

  <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
    @csrf
    @if(isset($user)) @method('PUT') @endif

    <div class="card" style="overflow:visible">

      {{-- Section: Identity --}}
      <div class="form-section">
        <div class="form-section-title">Identity</div>
        <div class="form-grid-2">
          <div>
            <label class="form-label">Full Name *</label>
            <input type="text" name="name" class="form-control @error('name') error @enderror"
                   value="{{ old('name', $user->name ?? '') }}" required placeholder="John Doe">
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="form-label">Email Address *</label>
            <input type="email" name="email" class="form-control @error('email') error @enderror"
                   value="{{ old('email', $user->email ?? '') }}" required placeholder="john@example.com">
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
          </div>
        </div>
        <div style="margin-top:16px">
          <label class="form-label">Phone Number</label>
          <input type="text" name="phone" class="form-control @error('phone') error @enderror"
                 value="{{ old('phone', $user->phone ?? '') }}" placeholder="+1 (555) 000-0000">
          @error('phone')<p class="form-error">{{ $message }}</p>@enderror
        </div>
      </div>

      {{-- Section: Password --}}
      <div class="form-section">
        <div class="form-section-title">{{ isset($user) ? 'Change Password' : 'Password' }}</div>
        @if(isset($user))
        <p style="font-size:0.8rem;color:#9ca3af;margin:0 0 16px">Leave both fields blank to keep the current password.</p>
        @endif
        <div class="form-grid-2">
          <div>
            <label class="form-label">{{ isset($user) ? 'New Password' : 'Password *' }}</label>
            <input type="password" name="password" class="form-control @error('password') error @enderror"
                   {{ isset($user) ? '' : 'required' }} autocomplete="new-password" placeholder="••••••••">
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control"
                   autocomplete="new-password" placeholder="••••••••">
          </div>
        </div>
      </div>

      {{-- Section: Role --}}
      <div class="form-section">
        <div class="form-section-title">Role</div>
        <div style="max-width:320px">
          <label class="form-label">Assign Role</label>
          <select name="role" class="form-control @error('role') error @enderror">
            <option value="">— No Role —</option>
            @foreach($roles as $role)
              <option value="{{ $role->name }}"
                {{ old('role', $user->roles->first()?->name ?? '') == $role->name ? 'selected' : '' }}>
                {{ $role->name }}
              </option>
            @endforeach
          </select>
          @error('role')<p class="form-error">{{ $message }}</p>@enderror
        </div>
      </div>

      {{-- Section: Companies --}}
      <div class="form-section">
        <div class="form-section-title">Company Access</div>
        @if($companies->isEmpty())
          <p style="font-size:0.85rem;color:#9ca3af">No active companies available.</p>
        @else
        <div class="company-grid">
          @foreach($companies as $co)
          <label class="company-check">
            <input type="checkbox" name="companies[]" value="{{ $co->id }}"
                   style="accent-color:var(--color-primary)"
                   {{ in_array($co->id, old('companies', isset($user) ? $user->companies->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
            <span>{{ $co->name }}</span>
          </label>
          @endforeach
        </div>
        @error('companies')<p class="form-error" style="margin-top:8px">{{ $message }}</p>@enderror
        @endif
      </div>

      {{-- Section: Access flags --}}
      <div class="form-section">
        <div class="form-section-title">Access Control</div>
        <div class="toggle-wrap">
          <label class="toggle-row" for="toggle_active">
            <div>
              <div class="toggle-info-name">Active Account</div>
              <div class="toggle-info-desc">Allow this user to log in to the platform</div>
            </div>
            <div class="toggle-switch">
              <input type="checkbox" id="toggle_active" name="is_active" value="1"
                     {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
              <span class="toggle-slider"></span>
            </div>
          </label>
          @if(auth()->user()->is_super_admin)
          <label class="toggle-row" for="toggle_super">
            <div>
              <div class="toggle-info-name">Super Admin</div>
              <div class="toggle-info-desc">Grant full platform access — all companies and admin panel</div>
            </div>
            <div class="toggle-switch">
              <input type="checkbox" id="toggle_super" name="is_super_admin" value="1"
                     {{ old('is_super_admin', $user->is_super_admin ?? false) ? 'checked' : '' }}>
              <span class="toggle-slider"></span>
            </div>
          </label>
          @endif
        </div>
      </div>

      {{-- Footer --}}
      <div class="form-footer">
        <button type="submit" class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            @if(isset($user))
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            @else
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            @endif
          </svg>
          {{ isset($user) ? 'Save Changes' : 'Create User' }}
        </button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
      </div>

    </div>
  </form>
</div>
@endsection
