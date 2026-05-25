@extends('layouts.app')
@section('title', 'Users')
@section('breadcrumb', 'Admin / Users')

@section('content')
<div class="page-header">
  <h1 class="page-title">Users</h1>
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ New User</a>
</div>

<div class="card">
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Companies</th>
          <th>Role</th>
          <th>Super Admin</th>
          <th>Status</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td style="font-weight:700">{{ $user->name }}</td>
          <td style="color:#6b7280">{{ $user->email }}</td>
          <td>
            <div style="display:flex;flex-wrap:wrap;gap:4px">
              @foreach($user->companies as $c)
                <span style="font-size:0.75rem;background:#ede9fe;color:#6366f1;padding:2px 8px;border-radius:99px">{{ $c->name }}</span>
              @endforeach
            </div>
          </td>
          <td>
            <div style="display:flex;flex-wrap:wrap;gap:4px">
              @foreach($user->roles as $role)
                <span style="font-size:0.75rem;background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:99px">{{ $role->name }}</span>
              @endforeach
            </div>
          </td>
          <td>{{ $user->is_super_admin ? '✓ Super Admin' : '—' }}</td>
          <td><span class="badge badge-{{ $user->is_active ? 'active' : 'inactive' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
          <td>
            <div style="display:flex;gap:6px;justify-content:flex-end">
              <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary btn-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
              </a>
              @if(!$user->is_super_admin)
              <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete user?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-xs">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg> Delete
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#9ca3af;padding:24px">No users found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($users->hasPages())
  <div style="padding:12px 16px;border-top:1px solid #f3f4f6">{{ $users->links() }}</div>
  @endif
</div>
@endsection
