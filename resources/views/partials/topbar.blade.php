<header class="topbar">
  <div class="flex items-center gap-4">
    {{-- Mobile toggle --}}
    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')"
            class="md:hidden p-2 text-gray-500 hover:text-indigo-600">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>

    {{-- Breadcrumb --}}
    <div style="font-size:0.85rem;color:#8b5cf6;font-weight:600">
      @yield('breadcrumb', 'Dashboard')
    </div>
  </div>

  <div class="flex items-center gap-3">
    {{-- Company switcher --}}
    <a href="{{ route('company.select') }}"
       style="display:flex;align-items:center;gap:6px;padding:6px 12px;background:#ede9fe;border-radius:8px;font-size:0.8rem;font-weight:600;color:#6d28d9;text-decoration:none">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      {{ $activeCompany->name ?? '' }}
    </a>

    {{-- User menu --}}
    <div style="position:relative" x-data="{ open: false }">
      <button @click="open = !open"
              style="display:flex;align-items:center;gap:8px;padding:6px 12px;border:1.5px solid #e5e7eb;border-radius:10px;background:#fff;font-size:0.85rem;font-weight:600;color:#374151;cursor:pointer">
        @if(auth()->user()->avatar)
          <img src="{{ auth()->user()->getAvatarUrlAttribute() }}" alt="" style="width:24px;height:24px;border-radius:50%;object-fit:cover">
        @else
          <div style="width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.65rem;font-weight:800">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
          </div>
        @endif
        {{ auth()->user()->name }}
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
      </button>

      <div x-show="open" @click.away="open = false"
           style="position:absolute;right:0;top:100%;margin-top:6px;background:#fff;border:1px solid #ede9fe;border-radius:12px;box-shadow:0 10px 40px rgba(99,102,241,0.15);min-width:180px;z-index:100;overflow:hidden">
        <a href="{{ route('password.change') }}"
           style="display:flex;align-items:center;gap:8px;padding:10px 16px;font-size:0.85rem;color:#374151;text-decoration:none">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Change Password
        </a>
        <div style="border-top:1px solid #f3f4f6;margin:2px 0"></div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
                  style="width:100%;display:flex;align-items:center;gap:8px;padding:10px 16px;font-size:0.85rem;color:#dc2626;background:none;border:none;cursor:pointer;text-align:left">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Sign Out
          </button>
        </form>
      </div>
    </div>
  </div>
</header>
