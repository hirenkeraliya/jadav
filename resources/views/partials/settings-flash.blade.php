@if(session('success'))
<div style="background:#d1fae5;border:1px solid #a7f3d0;color:#065f46;padding:12px 16px;border-radius:12px;margin-bottom:18px;font-size:0.875rem;font-weight:600;display:flex;align-items:center;gap:8px">
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
  {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:12px;margin-bottom:18px;font-size:0.875rem;font-weight:600;display:flex;align-items:center;gap:8px">
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
  {{ session('error') }}
</div>
@endif
