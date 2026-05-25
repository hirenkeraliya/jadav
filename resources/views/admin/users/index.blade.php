@extends('layouts.app')
@section('title', 'Users')
@section('breadcrumb', 'Admin / Users')

@push('styles')
<style>
  /* ── Admin Users Page ─────────────────────────────────────────── */
  .users-hero {
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
  .users-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: rgba(255,255,255,0.07);
    border-radius: 50%;
  }
  .users-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; right: 120px;
    width: 280px; height: 280px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
  }
  .users-hero-content { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap; }
  .users-hero h1 { font-size: 1.75rem; font-weight: 800; letter-spacing: -0.03em; margin: 0; }
  .users-hero p  { font-size: 0.9rem; opacity: 0.8; margin: 4px 0 0; }
  .btn-add-user {
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
  .btn-add-user:hover {
    background: rgba(255,255,255,0.25);
    border-color: rgba(255,255,255,0.5);
    transform: translateY(-1px);
    color: #fff;
  }

  /* Stats strip */
  .stat-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
  }
  .stat-tile {
    background: #fff;
    border-radius: 14px;
    padding: 16px 20px;
    border: 1px solid var(--color-primary-border);
    box-shadow: 0 1px 4px color-mix(in srgb, var(--color-primary) 6%, transparent);
    display: flex; flex-direction: column; gap: 4px;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .stat-tile:hover { transform: translateY(-2px); box-shadow: 0 6px 20px color-mix(in srgb, var(--color-primary) 10%, transparent); }
  .stat-tile-value { font-size: 1.75rem; font-weight: 800; color: var(--color-primary); line-height: 1; }
  .stat-tile-label { font-size: 0.72rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.08em; }
  .stat-tile-icon  { font-size: 1.4rem; margin-bottom: 4px; }
  .stat-tile.green .stat-tile-value { color: #10b981; }
  .stat-tile.amber .stat-tile-value { color: #f59e0b; }
  .stat-tile.red   .stat-tile-value { color: #ef4444; }

  /* Search + filter bar */
  .toolbar {
    display: flex; align-items: center; gap: 12px;
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
    flex-wrap: wrap;
  }
  .search-wrap {
    flex: 1; min-width: 220px; position: relative;
  }
  .search-wrap svg {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    color: #9ca3af; pointer-events: none;
  }
  .search-input {
    width: 100%;
    padding: 8px 12px 8px 36px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.875rem;
    outline: none;
    background: #fafaf9;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .search-input:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-primary) 12%, transparent);
    background: #fff;
  }
  .filter-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 20px;
    font-size: 0.78rem; font-weight: 600; cursor: pointer;
    border: 1.5px solid #e5e7eb; background: #fff;
    color: #6b7280; transition: all 0.15s; white-space: nowrap;
  }
  .filter-pill.active, .filter-pill:hover {
    border-color: var(--color-primary);
    background: var(--color-primary-subtle);
    color: var(--color-primary);
  }
  .filter-pill .dot { width: 7px; height: 7px; border-radius: 50%; }

  /* User avatar */
  .u-avatar {
    width: 38px; height: 38px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem; font-weight: 800; letter-spacing: -0.02em;
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
    color: #fff;
    box-shadow: 0 2px 8px color-mix(in srgb, var(--color-primary) 30%, transparent);
  }

  /* User name cell */
  .u-name-cell { display: flex; align-items: center; gap: 12px; }
  .u-name      { font-size: 0.9rem; font-weight: 700; color: #111827; }
  .u-email     { font-size: 0.78rem; color: #9ca3af; margin-top: 1px; }
  .u-super-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 0.68rem; font-weight: 700;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff; padding: 2px 7px; border-radius: 6px;
    margin-top: 3px; letter-spacing: 0.03em;
  }

  /* Company chips */
  .chip-company {
    display: inline-flex; align-items: center;
    font-size: 0.73rem; font-weight: 600;
    background: var(--color-primary-subtle);
    color: var(--color-primary);
    border: 1px solid var(--color-primary-border);
    padding: 2px 9px; border-radius: 20px;
  }

  /* Role badge */
  .chip-role {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 0.73rem; font-weight: 700;
    background: #fef3c7; color: #92400e;
    border: 1px solid #fde68a;
    padding: 3px 10px; border-radius: 20px;
  }

  /* Status pill */
  .status-pill {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 0.73rem; font-weight: 700; padding: 4px 10px; border-radius: 20px;
  }
  .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
  .status-active   { background: #d1fae5; color: #065f46; }
  .status-active   .dot { background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,0.5); }
  .status-inactive { background: #f3f4f6; color: #6b7280; }
  .status-inactive .dot { background: #d1d5db; }

  /* Action buttons */
  .action-group { display: flex; gap: 6px; justify-content: flex-end; align-items: center; }
  .btn-icon {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 8px;
    border: 1px solid #e5e7eb; background: #fff;
    color: #6b7280; cursor: pointer; transition: all 0.15s; text-decoration: none;
  }
  .btn-icon:hover         { border-color: var(--color-primary); background: var(--color-primary-subtle); color: var(--color-primary); }
  .btn-icon.danger:hover  { border-color: #fca5a5; background: #fef2f2; color: #dc2626; }
  .btn-icon.impersonate:hover { border-color: #a7f3d0; background: #f0fdf4; color: #059669; }

  /* Empty state */
  .empty-state { text-align: center; padding: 60px 24px; color: #9ca3af; }
  .empty-state svg { opacity: 0.3; margin-bottom: 16px; }
  .empty-state h3 { font-size: 1rem; font-weight: 600; color: #6b7280; margin: 0 0 6px; }
  .empty-state p  { font-size: 0.85rem; margin: 0; }

  /* Table row animation */
  .user-row { transition: background 0.15s; }
  .user-row:hover td { background: color-mix(in srgb, var(--color-primary) 2%, #fff); }
  .user-row.hidden-row { display: none; }
</style>
@endpush

@section('content')

{{-- Hero Header --}}
<div class="users-hero">
  <div class="users-hero-content">
    <div>
      <h1>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline;vertical-align:-4px;margin-right:8px"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        User Management
      </h1>
      <p>Manage all platform users, roles and company access</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn-add-user">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add New User
    </a>
  </div>
</div>

{{-- Stats Strip --}}
<div class="stat-strip">
  <div class="stat-tile">
    <div class="stat-tile-icon">👥</div>
    <div class="stat-tile-value">{{ $stats['total'] }}</div>
    <div class="stat-tile-label">Total Users</div>
  </div>
  <div class="stat-tile green">
    <div class="stat-tile-icon">✅</div>
    <div class="stat-tile-value">{{ $stats['active'] }}</div>
    <div class="stat-tile-label">Active</div>
  </div>
  <div class="stat-tile red">
    <div class="stat-tile-icon">⏸️</div>
    <div class="stat-tile-value">{{ $stats['inactive'] }}</div>
    <div class="stat-tile-label">Inactive</div>
  </div>
  <div class="stat-tile amber">
    <div class="stat-tile-icon">🛡️</div>
    <div class="stat-tile-value">{{ $stats['super_admin'] }}</div>
    <div class="stat-tile-label">Super Admins</div>
  </div>
</div>

{{-- Users Table Card --}}
<div class="card">

  {{-- Toolbar --}}
  <div class="toolbar">
    <div class="search-wrap">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input id="userSearch" type="text" class="search-input" placeholder="Search by name, email, company…">
    </div>
    <button class="filter-pill active" data-filter="all">
      All <span style="background:#e5e7eb;color:#374151;font-size:0.7rem;padding:1px 6px;border-radius:10px;margin-left:2px">{{ $stats['total'] }}</span>
    </button>
    <button class="filter-pill" data-filter="active">
      <span class="dot" style="background:#10b981"></span> Active
    </button>
    <button class="filter-pill" data-filter="inactive">
      <span class="dot" style="background:#d1d5db"></span> Inactive
    </button>
    <button class="filter-pill" data-filter="super">
      🛡 Super Admin
    </button>
  </div>

  {{-- Table --}}
  <div class="table-wrapper">
    <table class="table" id="usersTable">
      <thead>
        <tr>
          <th>User</th>
          <th>Companies</th>
          <th>Role</th>
          <th>Status</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        @php
          $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
          $hue = crc32($user->email) % 360;
        @endphp
        <tr class="user-row"
            data-status="{{ $user->is_active ? 'active' : 'inactive' }}"
            data-super="{{ $user->is_super_admin ? '1' : '0' }}"
            data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . $user->companies->pluck('name')->implode(' ')) }}">
          <td style="min-width:220px">
            <div class="u-name-cell">
              <div class="u-avatar" style="background:linear-gradient(135deg, hsl({{ $hue }},60%,48%) 0%, hsl({{ $hue }},70%,36%) 100%)">{{ $initials }}</div>
              <div>
                <div class="u-name">{{ $user->name }}</div>
                <div class="u-email">{{ $user->email }}</div>
                @if($user->is_super_admin)
                <div class="u-super-badge">
                  <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1l3 6 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1z"/></svg>
                  Super Admin
                </div>
                @endif
              </div>
            </div>
          </td>
          <td>
            <div style="display:flex;flex-wrap:wrap;gap:4px">
              @forelse($user->companies as $c)
                <span class="chip-company">{{ $c->name }}</span>
              @empty
                <span style="color:#d1d5db;font-size:0.8rem">—</span>
              @endforelse
            </div>
          </td>
          <td>
            @if($user->roles->isNotEmpty())
              @foreach($user->roles as $role)
                <span class="chip-role">
                  <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                  {{ $role->name }}
                </span>
              @endforeach
            @else
              <span style="color:#d1d5db;font-size:0.8rem">No role</span>
            @endif
          </td>
          <td>
            <span class="status-pill {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
              <span class="dot"></span>
              {{ $user->is_active ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td>
            <div class="action-group">
              {{-- Impersonate --}}
              @if(!$user->is($user) && auth()->id() !== $user->id)
              <form method="POST" action="{{ route('impersonate', $user) }}" style="display:contents">
                @csrf
                <button type="submit" class="btn-icon impersonate" title="Impersonate">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </button>
              </form>
              @endif
              {{-- Edit --}}
              <a href="{{ route('admin.users.edit', $user) }}" class="btn-icon" title="Edit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              {{-- Delete --}}
              @if(!$user->is_super_admin)
              <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:contents" onsubmit="return confirm('Permanently delete {{ $user->name }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-icon danger" title="Delete">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5">
            <div class="empty-state">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
              <h3>No users found</h3>
              <p>Create your first user to get started</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Footer --}}
  <div style="padding:12px 20px;border-top:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
    <p id="userCount" style="font-size:0.78rem;color:#9ca3af;margin:0">
      Showing <strong id="visibleCount">{{ $users->count() }}</strong> of {{ $users->count() }} users
    </p>
  </div>
</div>

@push('scripts')
<script>
(function () {
  const searchInput   = document.getElementById('userSearch');
  const filterBtns    = document.querySelectorAll('.filter-pill');
  const rows          = document.querySelectorAll('.user-row');
  const visibleCount  = document.getElementById('visibleCount');

  let activeFilter = 'all';
  let searchTerm   = '';

  function applyFilters() {
    let shown = 0;
    rows.forEach(row => {
      const matchSearch = !searchTerm || row.dataset.search.includes(searchTerm);
      const matchFilter =
        activeFilter === 'all'      ? true :
        activeFilter === 'active'   ? row.dataset.status === 'active' :
        activeFilter === 'inactive' ? row.dataset.status === 'inactive' :
        activeFilter === 'super'    ? row.dataset.super === '1' : true;

      if (matchSearch && matchFilter) {
        row.classList.remove('hidden-row');
        shown++;
      } else {
        row.classList.add('hidden-row');
      }
    });
    visibleCount.textContent = shown;
  }

  searchInput.addEventListener('input', e => {
    searchTerm = e.target.value.toLowerCase().trim();
    applyFilters();
  });

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeFilter = btn.dataset.filter;
      applyFilters();
    });
  });
})();
</script>
@endpush
@endsection
