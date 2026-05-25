@extends('layouts.app')
@section('title', 'Roles & Permissions')
@section('breadcrumb', 'Admin / Roles & Permissions')

@push('styles')
<style>
  .roles-hero {
    background: linear-gradient(135deg,
      color-mix(in srgb, var(--color-primary) 90%, #000) 0%,
      var(--color-primary) 50%,
      color-mix(in srgb, var(--color-primary) 70%, var(--color-secondary)) 100%
    );
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    color: #fff;
  }
  .roles-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: rgba(255,255,255,0.07);
    border-radius: 50%;
  }
  .roles-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; right: 120px;
    width: 280px; height: 280px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
  }
  .roles-hero-content { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap; }
  .roles-hero h1 { font-size: 1.75rem; font-weight: 800; letter-spacing: -0.03em; margin: 0; }
  .roles-hero p  { font-size: 0.9rem; opacity: 0.8; margin: 4px 0 0; }
  .btn-add-role {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(8px);
    color: #fff;
    border: 1.5px solid rgba(255,255,255,0.3);
    padding: 10px 20px; border-radius: 12px;
    font-size: 0.875rem; font-weight: 700;
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
  }
  .btn-add-role:hover {
    background: rgba(255,255,255,0.25);
    border-color: rgba(255,255,255,0.5);
    transform: translateY(-1px);
    color: #fff;
  }

  /* Role cards grid */
  .roles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
  }
  .role-card {
    background: #fff;
    border-radius: 16px;
    border: 1.5px solid var(--color-primary-border);
    box-shadow: 0 2px 8px color-mix(in srgb, var(--color-primary) 6%, transparent);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .role-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 28px color-mix(in srgb, var(--color-primary) 14%, transparent);
  }
  .role-card-header {
    padding: 20px 22px 16px;
    display: flex; align-items: center; gap: 14px;
    border-bottom: 1px solid #f3f4f6;
  }
  .role-icon {
    width: 46px; height: 46px; border-radius: 13px;
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
    display: flex; align-items: center; justify-content: center;
    color: #fff; flex-shrink: 0;
    box-shadow: 0 4px 12px color-mix(in srgb, var(--color-primary) 35%, transparent);
  }
  .role-name { font-size: 1.05rem; font-weight: 800; color: #111827; letter-spacing: -0.01em; }
  .role-meta { font-size: 0.78rem; color: #9ca3af; margin-top: 3px; }
  .role-card-body { padding: 16px 22px 20px; }
  .role-perm-count {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--color-primary-subtle);
    color: var(--color-primary);
    border: 1px solid var(--color-primary-border);
    padding: 4px 12px; border-radius: 20px;
    font-size: 0.78rem; font-weight: 700;
    margin-bottom: 14px;
  }
  .role-perm-preview {
    display: flex; flex-wrap: wrap; gap: 5px;
  }
  .perm-chip {
    display: inline-flex; align-items: center;
    font-size: 0.68rem; font-weight: 600;
    background: #f3f4f6; color: #6b7280;
    padding: 2px 8px; border-radius: 6px;
  }
  .perm-chip.has { background: color-mix(in srgb, var(--color-primary) 10%, #fff); color: var(--color-primary); }
  .role-card-footer {
    padding: 12px 22px;
    background: #fafaf9;
    border-top: 1px solid #f3f4f6;
    display: flex; align-items: center; justify-content: space-between;
  }
  .role-user-count {
    font-size: 0.78rem; color: #9ca3af; display: flex; align-items: center; gap: 5px;
  }
  .action-group { display: flex; gap: 6px; }
  .btn-icon {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 8px;
    border: 1px solid #e5e7eb; background: #fff;
    color: #6b7280; cursor: pointer; transition: all 0.15s; text-decoration: none;
  }
  .btn-icon:hover        { border-color: var(--color-primary); background: var(--color-primary-subtle); color: var(--color-primary); }
  .btn-icon.danger:hover { border-color: #fca5a5; background: #fef2f2; color: #dc2626; }

  /* Empty state */
  .empty-state { text-align: center; padding: 60px 24px; color: #9ca3af; }
  .empty-state svg { opacity: 0.3; margin-bottom: 16px; }
  .empty-state h3 { font-size: 1rem; font-weight: 600; color: #6b7280; margin: 0 0 6px; }
  .empty-state p  { font-size: 0.85rem; margin: 0; }

  /* Info banner */
  .info-banner {
    display: flex; align-items: flex-start; gap: 12px;
    background: color-mix(in srgb, var(--color-primary) 6%, #fff);
    border: 1px solid var(--color-primary-border);
    border-radius: 12px; padding: 14px 18px;
    margin-bottom: 24px; font-size: 0.82rem; color: #374151;
  }
  .info-banner svg { flex-shrink: 0; color: var(--color-primary); margin-top: 1px; }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="roles-hero">
  <div class="roles-hero-content">
    <div>
      <h1>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline;vertical-align:-4px;margin-right:8px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Roles &amp; Permissions
      </h1>
      <p>Define roles and control exactly what each role can do</p>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="btn-add-role">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Role
    </a>
  </div>
</div>

@if(session('success'))
<div style="background:#d1fae5;border:1px solid #a7f3d0;color:#065f46;padding:12px 16px;border-radius:12px;margin-bottom:20px;font-size:0.875rem;font-weight:600">
  ✓ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:12px;margin-bottom:20px;font-size:0.875rem;font-weight:600">
  ✗ {{ session('error') }}
</div>
@endif

<div class="info-banner">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  <span>Total permissions available: <strong>{{ $allPermissionsCount }}</strong>. Super Admins always have full access regardless of role.</span>
</div>

@if($roles->isEmpty())
  <div class="card">
    <div class="empty-state">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      <h3>No roles yet</h3>
      <p>Create your first role to get started.</p>
    </div>
  </div>
@else
<div class="roles-grid">
  @foreach($roles as $role)
  <div class="role-card">
    <div class="role-card-header">
      <div class="role-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      </div>
      <div>
        <div class="role-name">{{ $role->name }}</div>
        <div class="role-meta">{{ $role->permissions_count }} of {{ $allPermissionsCount }} permissions</div>
      </div>
    </div>
    <div class="role-card-body">
      <div class="role-perm-count">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ $role->permissions_count }} permissions granted
      </div>
      {{-- Preview bar: show per-module coverage --}}
      @php
        $rolePerms = $role->permissions->pluck('name')->toArray();
        $modules = ['customers','projects','tasks','finance','quotations','invoices','reports','settings','users','roles'];
      @endphp
      <div class="role-perm-preview">
        @foreach($modules as $mod)
          @php
            $hasAny = collect($rolePerms)->contains(fn($p) => str_starts_with($p, $mod.'.'));
          @endphp
          <span class="perm-chip {{ $hasAny ? 'has' : '' }}">{{ $mod }}</span>
        @endforeach
      </div>
    </div>
    <div class="role-card-footer">
      <div class="role-user-count">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}
      </div>
      <div class="action-group">
        <a href="{{ route('admin.roles.edit', $role) }}" class="btn-icon" title="Edit role">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </a>
        @if($role->users_count === 0)
        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" style="display:contents"
              onsubmit="return confirm('Delete role &quot;{{ addslashes($role->name) }}&quot;? This cannot be undone.')">
          @csrf @method('DELETE')
          <button type="submit" class="btn-icon danger" title="Delete role">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
          </button>
        </form>
        @else
        <button class="btn-icon danger" disabled title="Role is in use" style="opacity:0.35;cursor:not-allowed">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </button>
        @endif
      </div>
    </div>
  </div>
  @endforeach
</div>
@endif

@endsection
