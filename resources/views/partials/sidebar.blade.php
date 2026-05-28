<aside class="sidebar" id="sidebar">
  {{-- Logo --}}
  <div class="px-5 py-5 border-b border-white/10">
    @if($activeCompany->logo ?? false)
      <img src="{{ $activeCompany->getLogoUrlAttribute() }}" alt="logo" class="h-9 w-auto object-contain mb-1">
    @else
      <div style="font-size:1.1rem;font-weight:800;color:#fff;letter-spacing:-0.03em">
        {{ $activeCompany->name ?? 'Studio' }}
      </div>
    @endif
    <div style="font-size:0.7rem;color:#818cf8;font-weight:500">Interior Studio Suite</div>
  </div>

  {{-- Nav --}}
  <nav class="py-4">
    <a href="{{ route('dashboard') }}"
       class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      Dashboard
    </a>

    @canany(['customers.view', 'projects.view', 'tasks.view'])
    <div class="nav-group-label">Work</div>
    @endcanany

    @can('customers.view')
    <a href="{{ route('customers.index') }}"
       class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Clients
    </a>
    @endcan

    @can('projects.view')
    <a href="{{ route('projects.index') }}"
       class="nav-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Projects
    </a>
    @endcan

    @can('tasks.view')
    <a href="{{ route('tasks.mine') }}"
       class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
      Tasks
    </a>
    @endcan

    @can('quotations.view')
    <div class="nav-group-label">Finance</div>
    <a href="{{ route('quotations.index') }}"
       class="nav-item {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
      Quotations
    </a>
    @endcan

    @can('reports.view')
    <div class="nav-group-label">Insights</div>
    <a href="{{ route('reports.index') }}"
       class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      Reports
    </a>
    @endcan

    @can('payroll.view')
    <div class="nav-group-label">Payroll</div>
    <a href="{{ route('payroll.staff.index') }}"
       class="nav-item {{ request()->routeIs('payroll.staff.*') ? 'active' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" y1="11" x2="23" y2="11"/><line x1="20" y1="8" x2="20" y2="14"/></svg>
      Staff
    </a>
    <a href="{{ route('payroll.entries.index') }}"
       class="nav-item {{ request()->routeIs('payroll.entries.*') ? 'active' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Time Entries
    </a>
    <a href="{{ route('payroll.report.index') }}"
       class="nav-item {{ request()->routeIs('payroll.report.*') ? 'active' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/><line x1="9" y1="15" x2="13" y2="15"/></svg>
      Payroll Report
    </a>
    @endcan

    @can('settings.view')
    <div class="nav-group-label">Settings</div>
    <a href="{{ route('settings.company') }}"
       class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 0-14.14 0M4.93 19.07a10 10 0 0 0 14.14 0"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2"/></svg>
      Settings
    </a>
    @endcan

    @if(auth()->user()->is_super_admin)
    <div class="nav-group-label">Super Admin</div>
    <a href="{{ route('admin.users.index') }}"
       class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Users
    </a>
    <a href="{{ route('admin.companies.index') }}"
       class="nav-item {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      Companies
    </a>
    <a href="{{ route('admin.roles.index') }}"
       class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Roles &amp; Permissions
    </a>
    @endif
  </nav>
</aside>
