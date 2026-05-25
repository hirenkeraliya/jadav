<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — Interior Studio</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="min-height:100vh;display:flex;font-family:'Inter',sans-serif;background:#f8f7ff">

  {{-- Left decorative panel --}}
  <div style="display:none;flex-direction:column;justify-content:space-between;padding:48px;width:480px;min-height:100vh;background:linear-gradient(160deg,#1e1b4b 0%,#312e81 50%,#4c1d95 100%);position:relative;overflow:hidden"
       class="hidden lg:flex">
    {{-- Geometric accent --}}
    <div style="position:absolute;top:-80px;right:-80px;width:320px;height:320px;background:radial-gradient(circle,rgba(99,102,241,0.4),transparent 70%);border-radius:50%"></div>
    <div style="position:absolute;bottom:-60px;left:-60px;width:240px;height:240px;background:radial-gradient(circle,rgba(245,158,11,0.3),transparent 70%);border-radius:50%"></div>

    <div>
      <div style="font-size:1.4rem;font-weight:800;color:#fff;letter-spacing:-0.03em">Interior Studio</div>
      <div style="font-size:0.8rem;color:#818cf8;margin-top:2px">Project Management Suite</div>
    </div>

    <div>
      <div style="font-size:1.75rem;font-weight:800;color:#fff;line-height:1.25;margin-bottom:16px">
        Manage every project<br>with precision & style
      </div>
      <p style="color:#c7d2fe;font-size:0.9rem;line-height:1.6">
        From quotation to final invoice — track your interior design projects, finances, tasks, and clients in one elegant workspace.
      </p>
    </div>

    <div style="display:flex;gap:20px">
      <div style="background:rgba(255,255,255,0.08);border-radius:12px;padding:16px 20px;flex:1;border:1px solid rgba(255,255,255,0.1)">
        <div style="font-size:1.6rem;font-weight:800;color:#fbbf24">∞</div>
        <div style="font-size:0.75rem;color:#c7d2fe;margin-top:2px">Unlimited Projects</div>
      </div>
      <div style="background:rgba(255,255,255,0.08);border-radius:12px;padding:16px 20px;flex:1;border:1px solid rgba(255,255,255,0.1)">
        <div style="font-size:1.6rem;font-weight:800;color:#a78bfa">PDF</div>
        <div style="font-size:0.75rem;color:#c7d2fe;margin-top:2px">One-click Invoices</div>
      </div>
    </div>
  </div>

  {{-- Right sign-in panel --}}
  <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:32px">
    <div style="width:100%;max-width:420px">

      {{-- Mobile logo --}}
      <div class="lg:hidden" style="text-align:center;margin-bottom:32px">
        <div style="font-size:1.4rem;font-weight:800;color:#1e1b4b">Interior Studio</div>
        <div style="font-size:0.8rem;color:#8b5cf6">Project Management Suite</div>
      </div>

      <h1 style="font-size:1.75rem;font-weight:800;color:#1e1b4b;letter-spacing:-0.03em;margin-bottom:6px">Welcome back</h1>
      <p style="font-size:0.9rem;color:#8b5cf6;margin-bottom:32px">Sign in to continue to your workspace</p>

      @if($errors->any())
      <div class="alert alert-error mb-5">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errors->first() }}</span>
      </div>
      @endif

      <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div style="margin-bottom:18px">
          <label class="form-label">Email address</label>
          <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'error' : '' }}"
                 value="{{ old('email') }}" placeholder="you@company.com" required autofocus>
        </div>

        <div style="margin-bottom:24px">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px">
            <label class="form-label" style="margin:0">Password</label>
          </div>
          <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'error' : '' }}"
                 placeholder="••••••••" required>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:0.85rem;color:#6b7280">
            <input type="checkbox" name="remember" style="accent-color:#6366f1"> Remember me
          </label>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;padding:11px;font-size:0.95rem">
          Sign in →
        </button>
      </form>
    </div>
  </div>

</body>
</html>
