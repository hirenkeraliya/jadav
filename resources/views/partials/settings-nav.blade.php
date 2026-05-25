<nav style="width:200px;flex-shrink:0">
  <div style="background:#fff;border:1px solid var(--color-primary-border);border-radius:14px;overflow:hidden">
    @php
      $navLinks = [
        ['route' => 'settings.company', 'label' => 'Company', 'icon' => 'M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z'],
        ['route' => 'settings.project-types', 'label' => 'Project Types', 'icon' => 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'],
        ['route' => 'settings.payment-types', 'label' => 'Payment Types', 'icon' => 'M1 10h22M8 4h8M2 10v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V10'],
        ['route' => 'settings.finance-entry-types', 'label' => 'Finance Types', 'icon' => 'M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'],
        ['route' => 'settings.terms', 'label' => 'Terms Templates', 'icon' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z'],
        ['route' => 'settings.custom-fields', 'label' => 'Custom Fields', 'icon' => 'M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z'],
        ['route' => 'settings.users', 'label' => 'Users', 'icon' => 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75'],
      ];
    @endphp
    @foreach($navLinks as $link)
    <a href="{{ route($link['route']) }}"
       style="display:flex;align-items:center;gap:10px;padding:11px 16px;font-size:0.85rem;font-weight:{{ request()->routeIs($link['route']) ? '700' : '500' }};color:{{ request()->routeIs($link['route']) ? 'var(--color-primary)' : '#6b7280' }};text-decoration:none;background:{{ request()->routeIs($link['route']) ? 'var(--color-primary-subtle)' : 'transparent' }};border-bottom:1px solid #f3f4f6">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="{{ $link['icon'] }}"/></svg>
      {{ $link['label'] }}
    </a>
    @endforeach
  </div>
</nav>
