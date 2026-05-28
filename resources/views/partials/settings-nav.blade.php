<style>
  .settings-nav-wrap {
    width: 220px;
    flex-shrink: 0;
  }
  .settings-nav-card {
    background: #fff;
    border: 1.5px solid var(--color-primary-border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 10px color-mix(in srgb, var(--color-primary) 6%, transparent);
    position: sticky;
    top: 24px;
  }
  .settings-nav-header {
    padding: 16px 18px 12px;
    border-bottom: 1px solid #f3f4f6;
    background: var(--color-primary-subtle);
  }
  .settings-nav-header-title {
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--color-primary);
    display: flex;
    align-items: center;
    gap: 7px;
  }
  .settings-nav-group-label {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #d1d5db;
    padding: 12px 18px 5px;
  }
  .settings-nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px;
    font-size: 0.845rem;
    font-weight: 500;
    color: #6b7280;
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: all 0.15s;
    position: relative;
  }
  .settings-nav-link:hover {
    color: var(--color-primary);
    background: var(--color-primary-subtle);
    border-left-color: color-mix(in srgb, var(--color-primary) 40%, transparent);
  }
  .settings-nav-link.active {
    color: var(--color-primary);
    background: var(--color-primary-subtle);
    border-left-color: var(--color-primary);
    font-weight: 700;
  }
  .settings-nav-link .nav-icon {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    flex-shrink: 0;
    transition: background 0.15s;
  }
  .settings-nav-link.active .nav-icon,
  .settings-nav-link:hover .nav-icon {
    background: color-mix(in srgb, var(--color-primary) 15%, #fff);
  }
  .settings-nav-divider {
    height: 1px;
    background: #f3f4f6;
    margin: 4px 0;
  }
</style>

<nav class="settings-nav-wrap">
  <div class="settings-nav-card">
    <div class="settings-nav-header">
      <div class="settings-nav-header-title">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 0-14.14 0M4.93 19.07a10 10 0 0 0 14.14 0"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2"/></svg>
        Settings
      </div>
    </div>

    <div class="settings-nav-group-label">General</div>

    <a href="{{ route('settings.company') }}"
       class="settings-nav-link {{ request()->routeIs('settings.company') ? 'active' : '' }}">
      <span class="nav-icon">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/></svg>
      </span>
      Company
    </a>

    <a href="{{ route('settings.users') }}"
       class="settings-nav-link {{ request()->routeIs('settings.users*') ? 'active' : '' }}">
      <span class="nav-icon">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </span>
      Users
    </a>

    <div class="settings-nav-divider"></div>
    <div class="settings-nav-group-label">Lookups</div>

    <a href="{{ route('settings.project-types') }}"
       class="settings-nav-link {{ request()->routeIs('settings.project-types*') ? 'active' : '' }}">
      <span class="nav-icon">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      </span>
      Project Types
    </a>

    <a href="{{ route('settings.payment-types') }}"
       class="settings-nav-link {{ request()->routeIs('settings.payment-types*') ? 'active' : '' }}">
      <span class="nav-icon">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
      </span>
      Payment Types
    </a>

    <a href="{{ route('settings.finance-entry-types') }}"
       class="settings-nav-link {{ request()->routeIs('settings.finance-entry-types*') ? 'active' : '' }}">
      <span class="nav-icon">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </span>
      Finance Types
    </a>

    <div class="settings-nav-divider"></div>
    <div class="settings-nav-group-label">Documents</div>

    <a href="{{ route('settings.terms') }}"
       class="settings-nav-link {{ request()->routeIs('settings.terms*') ? 'active' : '' }}">
      <span class="nav-icon">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      </span>
      Terms Templates
    </a>

    <a href="{{ route('settings.custom-fields') }}"
       class="settings-nav-link {{ request()->routeIs('settings.custom-fields*') ? 'active' : '' }}">
      <span class="nav-icon">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
      </span>
      Custom Fields
    </a>

  </div>
</nav>
