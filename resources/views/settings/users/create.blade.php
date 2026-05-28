@extends('layouts.app')
@section('title', 'Add User')
@section('breadcrumb')
  <a href="{{ route('settings.users') }}" style="color:#8b5cf6;text-decoration:none">Settings / Users</a> / Add User
@endsection

@section('content')
<div style="display:flex;gap:20px;max-width:1100px">
  @include('partials.settings-nav')
  <div style="flex:1">
    <h1 class="page-title" style="margin-bottom:24px">Add User</h1>

    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('settings.users.store') }}"
              data-autosave
              data-autosave-key="settings-user::new">
          @csrf

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
            <div>
              <label class="form-label">Name <span style="color:#ef4444">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="form-label">Email <span style="color:#ef4444">*</span></label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="form-label">Password <span style="color:#ef4444">*</span></label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="form-label">Confirm Password <span style="color:#ef4444">*</span></label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div>
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div>
              <label class="form-label">Role <span style="color:#ef4444">*</span></label>
              <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                <option value="">— Select Role —</option>
                @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                @endforeach
              </select>
              @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div style="margin-bottom:20px">
            <label class="form-label">Companies <span style="color:#ef4444">*</span></label>
            @error('companies')<div style="color:#ef4444;font-size:0.82rem;margin-bottom:6px">{{ $message }}</div>@enderror
            <div style="display:flex;flex-wrap:wrap;gap:10px">
              @foreach($companies as $co)
              <label style="display:flex;align-items:center;gap:6px;font-size:0.875rem;cursor:pointer">
                <input type="checkbox" name="companies[]" value="{{ $co->id }}" {{ in_array($co->id, old('companies', [])) ? 'checked' : '' }}>
                {{ $co->name }}
              </label>
              @endforeach
            </div>
          </div>

          <div style="display:flex;gap:10px">
            <button type="submit" class="btn btn-primary">Create User</button>
            <a href="{{ route('settings.users') }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
