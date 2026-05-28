@extends('layouts.app')
@section('title', isset($role) ? 'Edit Role' : 'New Role')
@section('breadcrumb')
  <a href="{{ route('admin.roles.index') }}" style="color:var(--color-primary);text-decoration:none;font-weight:600">Roles &amp; Permissions</a>
  <span style="color:#d1d5db;margin:0 6px">/</span>
  {{ isset($role) ? 'Edit' : 'New Role' }}
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
  .form-page-title    { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; color: var(--color-primary-text-dark); margin: 0; }
  .form-page-subtitle { font-size: 0.85rem; color: #9ca3af; margin: 3px 0 0; }

  .form-section { padding: 24px; border-bottom: 1px solid #f3f4f6; }
  .form-section:last-of-type { border-bottom: none; }
  .form-section-title {
    font-size: 0.78rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.09em; color: var(--color-primary);
    margin-bottom: 18px; display: flex; align-items: center; gap: 8px;
  }
  .form-section-title::after {
    content: ''; flex: 1; height: 1px; background: var(--color-primary-border);
  }

  /* Module permission blocks */
  .module-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 14px;
  }
  .module-block {
    border: 1.5px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    transition: border-color 0.2s;
  }
  .module-block:has(.perm-cb:checked) {
    border-color: var(--color-primary);
  }
  .module-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 16px;
    background: #fafaf9;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
  }
  .module-name {
    display: flex; align-items: center; gap: 8px;
    font-size: 0.875rem; font-weight: 700; color: #111827;
    text-transform: capitalize;
  }
  .module-icon {
    width: 28px; height: 28px; border-radius: 8px;
    background: var(--color-primary-subtle);
    display: flex; align-items: center; justify-content: center;
    color: var(--color-primary);
  }
  .module-select-all {
    font-size: 0.72rem; font-weight: 600; color: var(--color-primary);
    cursor: pointer; background: none; border: none; padding: 4px 8px;
    border-radius: 6px; transition: background 0.15s;
  }
  .module-select-all:hover { background: var(--color-primary-subtle); }
  .module-perms {
    display: flex; flex-wrap: wrap; gap: 8px;
    padding: 14px 16px;
  }
  .perm-toggle {
    display: flex; align-items: center; gap: 7px;
    padding: 7px 14px; border-radius: 20px;
    border: 1.5px solid #e5e7eb;
    cursor: pointer; font-size: 0.8rem; font-weight: 600;
    color: #6b7280; background: #fff;
    transition: all 0.15s; user-select: none;
  }
  .perm-toggle:hover { border-color: var(--color-primary); color: var(--color-primary); }
  .perm-toggle:has(input:checked) {
    border-color: var(--color-primary);
    background: var(--color-primary-subtle);
    color: var(--color-primary);
  }
  .perm-cb { display: none; }

  /* action icons per permission */
  .perm-action-view   { color: #6366f1; }
  .perm-action-create { color: #10b981; }
  .perm-action-edit   { color: #f59e0b; }
  .perm-action-delete { color: #ef4444; }

  /* Select all / none bar */
  .bulk-bar {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 18px; flex-wrap: wrap;
  }
  .bulk-btn {
    font-size: 0.78rem; font-weight: 600; cursor: pointer;
    background: none; border: 1.5px solid #e5e7eb;
    color: #6b7280; padding: 5px 14px; border-radius: 20px;
    transition: all 0.15s;
  }
  .bulk-btn:hover { border-color: var(--color-primary); color: var(--color-primary); background: var(--color-primary-subtle); }

  /* Permission counter badge */
  .perm-counter {
    margin-left: auto;
    font-size: 0.75rem; font-weight: 700;
    color: var(--color-primary);
    background: var(--color-primary-subtle);
    border: 1px solid var(--color-primary-border);
    padding: 3px 10px; border-radius: 20px;
  }

  .form-footer {
    padding: 20px 24px;
    border-top: 1px solid #f3f4f6;
    display: flex; align-items: center; gap: 10px;
    background: #fafaf9; border-radius: 0 0 16px 16px;
  }
</style>
@endpush

@section('content')
<div style="max-width:900px">

  <div class="form-page-header">
    <div class="form-page-icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    </div>
    <div>
      <h1 class="form-page-title">{{ isset($role) ? 'Edit Role: ' . $role->name : 'Create New Role' }}</h1>
      <p class="form-page-subtitle">{{ isset($role) ? 'Update role name and its assigned permissions' : 'Define a name and select which permissions this role grants' }}</p>
    </div>
  </div>

  <form method="POST" action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}"
        data-autosave
        data-autosave-key="role::{{ $role->id ?? 'new' }}">
    @csrf
    @if(isset($role)) @method('PUT') @endif

    <div class="card" style="overflow:visible">

      {{-- Role Name --}}
      <div class="form-section">
        <div class="form-section-title">Role Identity</div>
        <div style="max-width:400px">
          <label class="form-label">Role Name *</label>
          <input type="text" name="name"
                 class="form-control @error('name') error @enderror"
                 value="{{ old('name', $role->name ?? '') }}"
                 required placeholder="e.g. Designer, Accountant, Viewer">
          @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>
      </div>

      {{-- Permissions --}}
      <div class="form-section">
        <div class="form-section-title">
          Permissions
          <span class="perm-counter" id="permCounter">0 selected</span>
        </div>

        <div class="bulk-bar">
          <button type="button" class="bulk-btn" id="selectAll">Select All</button>
          <button type="button" class="bulk-btn" id="selectNone">Deselect All</button>
        </div>

        @if($permissions->isEmpty())
          <p style="color:#9ca3af;font-size:0.85rem">
            No permissions found. Run <code>php artisan db:seed --class=PermissionSeeder</code> to populate them.
          </p>
        @else
        <div class="module-grid">
          @foreach($permissions as $module => $modulePerms)
          @php
            $moduleIcons = [
              'customers'  => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>',
              'projects'   => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
              'tasks'      => '<polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
              'finance'    => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
              'quotations' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>',
              'invoices'   => '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
              'reports'    => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
              'settings'   => '<circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 0-14.14 0"/>',
              'users'      => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
              'roles'      => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
            ];
            $icon = $moduleIcons[$module] ?? '<circle cx="12" cy="12" r="10"/>';
          @endphp
          <div class="module-block" data-module="{{ $module }}">
            <div class="module-header">
              <div class="module-name">
                <div class="module-icon">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $icon !!}</svg>
                </div>
                {{ ucfirst($module) }}
              </div>
              <button type="button" class="module-select-all" data-module="{{ $module }}">All</button>
            </div>
            <div class="module-perms">
              @foreach($modulePerms as $perm)
              @php
                $action = explode('.', $perm->name)[1] ?? $perm->name;
                $isChecked = isset($rolePermissions) ? in_array($perm->name, $rolePermissions) : false;
                $isOld = in_array($perm->name, old('permissions', []));
                $checked = (old('permissions') !== null) ? $isOld : $isChecked;
                $actionColors = ['view' => '#6366f1', 'create' => '#10b981', 'edit' => '#f59e0b', 'delete' => '#ef4444'];
                $dotColor = $actionColors[$action] ?? '#9ca3af';
              @endphp
              <label class="perm-toggle">
                <input type="checkbox" class="perm-cb" name="permissions[]"
                       value="{{ $perm->name }}" {{ $checked ? 'checked' : '' }}>
                <svg width="8" height="8" viewBox="0 0 8 8" fill="{{ $dotColor }}"><circle cx="4" cy="4" r="4"/></svg>
                {{ ucfirst($action) }}
              </label>
              @endforeach
            </div>
          </div>
          @endforeach
        </div>
        @endif
      </div>

      {{-- Footer --}}
      <div class="form-footer">
        <button type="submit" class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          {{ isset($role) ? 'Save Changes' : 'Create Role' }}
        </button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
      </div>

    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
  function countChecked() {
    const n = document.querySelectorAll('.perm-cb:checked').length;
    document.getElementById('permCounter').textContent = n + ' selected';
  }

  // Live counter
  document.querySelectorAll('.perm-cb').forEach(cb => {
    cb.addEventListener('change', countChecked);
  });
  countChecked();

  // Module "All" buttons
  document.querySelectorAll('.module-select-all').forEach(btn => {
    btn.addEventListener('click', () => {
      const mod = btn.dataset.module;
      const cbs = document.querySelectorAll(`.module-block[data-module="${mod}"] .perm-cb`);
      const allChecked = Array.from(cbs).every(c => c.checked);
      cbs.forEach(c => c.checked = !allChecked);
      countChecked();
    });
  });

  // Global select/deselect
  document.getElementById('selectAll').addEventListener('click', () => {
    document.querySelectorAll('.perm-cb').forEach(c => c.checked = true);
    countChecked();
  });
  document.getElementById('selectNone').addEventListener('click', () => {
    document.querySelectorAll('.perm-cb').forEach(c => c.checked = false);
    countChecked();
  });
})();
</script>
@endpush
