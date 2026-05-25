<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Select Company — Interior Studio</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#f8f7ff 0%,#ede9fe 100%);font-family:'Inter',sans-serif;padding:20px">
  <div style="width:100%;max-width:480px">
    <div style="text-align:center;margin-bottom:32px">
      <div style="display:inline-flex;align-items:center;justify-content:center;width:56px;height:56px;background:linear-gradient(135deg,#6366f1,#4f46e5);border-radius:16px;margin-bottom:16px;box-shadow:0 8px 24px rgba(99,102,241,0.3)">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      </div>
      <h1 style="font-size:1.6rem;font-weight:800;color:#1e1b4b;letter-spacing:-0.02em">Select Workspace</h1>
      <p style="color:#8b5cf6;font-size:0.9rem;margin-top:4px">Choose which company to work in</p>
    </div>

    <div class="card" style="overflow:visible">
      <div class="card-body">
        <form method="POST" action="{{ route('company.select.post') }}">
          @csrf
          <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:24px">
            @foreach($companies as $company)
            <label style="display:flex;align-items:center;gap:14px;padding:14px 16px;border:2px solid {{ $defaultCompanyId === $company->id ? '#6366f1' : '#e5e7eb' }};border-radius:12px;cursor:pointer;transition:all 0.2s;background:{{ $defaultCompanyId === $company->id ? '#f5f3ff' : '#fff' }}"
                   for="company_{{ $company->id }}"
                   onmouseover="this.style.borderColor='#6366f1';this.style.background='#f5f3ff'"
                   onmouseout="this.style.borderColor='{{ $defaultCompanyId === $company->id ? '#6366f1' : '#e5e7eb' }}';this.style.background='{{ $defaultCompanyId === $company->id ? '#f5f3ff' : '#fff' }}'">
              <input type="radio" name="company_id" value="{{ $company->id }}" id="company_{{ $company->id }}"
                     {{ $defaultCompanyId === $company->id ? 'checked' : '' }}
                     style="accent-color:#6366f1;width:18px;height:18px">
              <div style="display:flex;align-items:center;gap:12px;flex:1">
                @if($company->logo)
                  <img src="{{ $company->getLogoUrlAttribute() }}" style="width:36px;height:36px;object-fit:contain;border-radius:8px">
                @else
                  <div style="width:36px;height:36px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:0.85rem">
                    {{ strtoupper(substr($company->name, 0, 2)) }}
                  </div>
                @endif
                <div>
                  <div style="font-weight:700;color:#1e1b4b;font-size:0.95rem">{{ $company->name }}</div>
                  @if($company->email)
                    <div style="font-size:0.78rem;color:#8b5cf6">{{ $company->email }}</div>
                  @endif
                </div>
              </div>
              @if($defaultCompanyId === $company->id)
                <span class="badge badge-active" style="font-size:0.65rem">Default</span>
              @endif
            </label>
            @endforeach
          </div>

          <button type="submit" class="btn btn-primary" style="width:100%;padding:11px">
            Continue to Dashboard →
          </button>
        </form>
      </div>
    </div>

    <div style="text-align:center;margin-top:16px">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="font-size:0.85rem;color:#8b5cf6;background:none;border:none;cursor:pointer">
          Sign out
        </button>
      </form>
    </div>
  </div>
</body>
</html>
