<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Studio') — {{ $activeCompany->name ?? 'Dashboard' }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @php
    $p = $primaryColor ?? '#6366f1';
    $s = $secondaryColor ?? '#f59e0b';
  @endphp
  <style>
    :root {
      --color-primary: {{ $p }};
      --color-secondary: {{ $s }};
      --color-primary-dark:      color-mix(in srgb, {{ $p }} 82%, #000);
      --color-primary-light:     color-mix(in srgb, {{ $p }} 65%, #fff);
      --color-primary-subtle:    color-mix(in srgb, {{ $p }}  8%, #fff);
      --color-primary-border:    color-mix(in srgb, {{ $p }} 22%, #fff);
      --color-primary-text-dark: color-mix(in srgb, {{ $p }} 55%, #000);
    }
    /* Sidebar — rendered server-side so it always reflects the company color */
    .sidebar {
      background: linear-gradient(
        180deg,
        color-mix(in srgb, {{ $p }} 25%, #000) 0%,
        color-mix(in srgb, {{ $p }} 42%, #000) 55%,
        color-mix(in srgb, {{ $p }} 25%, #000) 100%
      ) !important;
    }
    .nav-item.active::before {
      background: {{ $s }} !important;
    }
  </style>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @stack('styles')
</head>
<body>

{{-- Impersonation banner --}}
@if(auth()->user()->isImpersonating())
<div class="impersonation-banner">
  <span>⚠️ Impersonating <strong>{{ auth()->user()->name }}</strong></span>
  <form method="POST" action="{{ route('impersonate.leave') }}">
    @csrf
    <button type="submit" class="btn btn-xs" style="background:#fff3;color:#fff;border:1px solid #fff5">Leave Impersonation</button>
  </form>
</div>
@endif

<div class="flex" style="min-height:100vh">

  {{-- Sidebar --}}
  @include('partials.sidebar')

  {{-- Main content --}}
  <div class="main-content flex-1 flex flex-col">

    {{-- Topbar --}}
    @include('partials.topbar')

    {{-- Page content --}}
    <main class="flex-1 p-6">
      @include('partials.flash')
      @yield('content')
    </main>

  </div>
</div>

{{-- Global auto-save for forms with data-autosave --}}
<script>
(function () {
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-autosave]').forEach(initAutoSave);
  });

  function initAutoSave(form) {
    const key = 'autosave::' + (form.dataset.autosaveKey || location.pathname);
    let saveTimer = null;
    const originals = {};

    // 1. Capture server-rendered originals BEFORE restoring any draft
    snapshot(originals);

    // 2. Restore draft if one exists
    const raw = localStorage.getItem(key);
    if (raw) {
      try {
        const draft = JSON.parse(raw);
        restoreDraft(draft);
        showDraftBanner(key, draft._at);
      } catch (e) {
        localStorage.removeItem(key);
      }
    }

    // 3. Highlight any fields that differ from originals
    refreshIndicators();

    // 4. Watch changes
    form.addEventListener('input',  handleChange);
    form.addEventListener('change', handleChange);

    // 5. Clear draft on successful submit
    form.addEventListener('submit', function () {
      localStorage.removeItem(key);
    });

    /* ── helpers ── */

    function handleChange() {
      refreshIndicators();
      clearTimeout(saveTimer);
      saveTimer = setTimeout(persistDraft, 900);
    }

    function persistDraft() {
      const data = collect();
      data._at = new Date().toISOString();
      localStorage.setItem(key, JSON.stringify(data));
      showSavedToast();
    }

    function collect() {
      const data = {};
      inputs().forEach(el => {
        if (el.type === 'checkbox') {
          const arrKey = el.name;
          if (!(arrKey in data)) data[arrKey] = [];
          if (el.checked) data[arrKey].push(el.value);
        } else if (el.type === 'radio') {
          if (el.checked) data[el.name] = el.value;
        } else {
          data[el.name] = el.value;
        }
      });
      return data;
    }

    function snapshot(target) {
      inputs().forEach(el => {
        if (el.type === 'checkbox') {
          const k = el.name;
          if (!(k in target)) target[k] = [];
          if (el.checked) target[k].push(el.value);
        } else if (el.type === 'radio') {
          if (el.checked) target[el.name] = el.value;
        } else {
          target[el.name] = el.value;
        }
      });
    }

    function restoreDraft(data) {
      // Reset all checkboxes for array fields first
      inputs().filter(el => el.type === 'checkbox').forEach(el => {
        if (el.name in data) el.checked = false;
      });
      inputs().forEach(el => {
        if (!(el.name in data)) return;
        if (el.type === 'checkbox') {
          el.checked = Array.isArray(data[el.name]) && data[el.name].includes(el.value);
        } else if (el.type === 'radio') {
          el.checked = (data[el.name] === el.value);
        } else {
          el.value = data[el.name];
        }
      });
    }

    function refreshIndicators() {
      const current = collect();
      inputs().forEach(el => {
        let changed = false;
        if (el.type === 'checkbox') {
          const origArr = originals[el.name] || [];
          const currArr = current[el.name] || [];
          const wasChecked = origArr.includes(el.value);
          const isChecked  = currArr.includes(el.value);
          changed = wasChecked !== isChecked;
        } else if (el.type === 'radio') {
          changed = (originals[el.name] || '') !== (current[el.name] || '');
        } else {
          changed = (originals[el.name] || '') !== (current[el.name] || '');
        }
        applyFieldStyle(el, changed);
      });

      // Show/hide the unsaved-changes notice
      const hasAny = inputs().some(el => {
        if (el.type === 'checkbox') {
          const origArr = originals[el.name] || [];
          const currArr = current[el.name] || [];
          return origArr.includes(el.value) !== currArr.includes(el.value);
        }
        return (originals[el.name] || '') !== (current[el.name] || '');
      });
      const notice = document.getElementById('autosave-pending-' + safeId(key));
      if (notice) notice.style.display = hasAny ? 'flex' : 'none';
    }

    function applyFieldStyle(el, changed) {
      if (el.type === 'checkbox' || el.type === 'radio') {
        // For checkboxes/radios style the label wrapper
        const label = el.closest('label');
        if (label) {
          label.style.outline = changed ? '2px solid #f59e0b' : '';
          label.style.outlineOffset = changed ? '1px' : '';
        }
        return;
      }
      el.style.borderLeftWidth  = changed ? '3px' : '';
      el.style.borderLeftStyle  = changed ? 'solid' : '';
      el.style.borderLeftColor  = changed ? '#f59e0b' : '';
      el.style.backgroundColor  = changed ? '#fffdf0' : '';
      el.style.transition       = 'border-left-color 0.2s, background-color 0.2s';
    }

    function showDraftBanner(storageKey, savedAt) {
      if (document.getElementById('autosave-banner-' + safeId(storageKey))) return;
      const timeStr = savedAt
        ? new Date(savedAt).toLocaleString(undefined, { day:'numeric', month:'short', hour:'2-digit', minute:'2-digit' })
        : 'earlier';
      const banner = document.createElement('div');
      banner.id = 'autosave-banner-' + safeId(storageKey);
      banner.style.cssText = 'display:flex;align-items:center;gap:12px;background:#fffbeb;border:1px solid #fbbf24;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:0.85rem';
      banner.innerHTML =
        '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' +
        '<span style="flex:1;color:#92400e"><strong>Draft restored</strong> — Unsaved changes from ' + timeStr + '</span>' +
        '<button type="button" style="padding:4px 12px;border:1px solid #f59e0b;border-radius:6px;background:#fff;color:#92400e;cursor:pointer;font-size:0.8rem;font-weight:600" ' +
        'onclick="localStorage.removeItem(\'' + storageKey.replace(/'/g, "\\'") + '\');location.reload()">Discard draft</button>';
      form.insertBefore(banner, form.firstChild);
    }

    function inputs() {
      return Array.from(form.querySelectorAll(
        'input:not([type=file]):not([type=hidden]):not([type=submit]), textarea, select'
      )).filter(el => el.name && el.name !== '_token' && el.name !== '_method');
    }

    function safeId(str) { return str.replace(/[^a-z0-9]/gi, '_'); }

    function showSavedToast() {
      const id = 'autosave-toast';
      let toast = document.getElementById(id);
      if (!toast) {
        toast = document.createElement('div');
        toast.id = id;
        toast.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;background:#1e1b4b;color:#fff;border-radius:10px;padding:10px 18px;font-size:0.82rem;font-weight:600;display:flex;align-items:center;gap:8px;box-shadow:0 4px 20px rgba(0,0,0,0.25);transition:opacity 0.3s';
        toast.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Draft saved';
        document.body.appendChild(toast);
      }
      toast.style.opacity = '1';
      clearTimeout(toast._t);
      toast._t = setTimeout(() => { toast.style.opacity = '0'; }, 2500);
    }
  }
})();
</script>

@stack('scripts')
</body>
</html>
