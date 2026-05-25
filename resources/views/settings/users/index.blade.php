@extends('layouts.app')
@section('title', 'Users')
@section('breadcrumb', 'Settings / Users')

@section('content')
<div style="display:flex;gap:20px;max-width:1100px">
  @include('partials.settings-nav')
  <div style="flex:1">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
      <h1 class="page-title">Users</h1>
      <a href="{{ route('settings.users.create') }}" class="btn btn-primary">+ Add User</a>
    </div>

    <div class="card">
      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Role</th>
              <th style="text-align:right">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $user)
            <tr>
              <td style="font-weight:600">{{ $user->name }}</td>
              <td style="color:#6b7280">{{ $user->email }}</td>
              <td style="color:#6b7280">{{ $user->phone ?? '—' }}</td>
              <td>
                @foreach($user->roles as $role)
                  <span class="badge badge-active">{{ ucfirst($role->name) }}</span>
                @endforeach
              </td>
              <td>
                <div style="display:flex;gap:6px;justify-content:flex-end">
                  <a href="{{ route('settings.users.edit', $user) }}" class="btn btn-secondary btn-xs">Edit</a>
                  @if(!$user->is_super_admin)
                  <form method="POST" action="{{ route('settings.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                  </form>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#9ca3af;padding:20px">No users yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
