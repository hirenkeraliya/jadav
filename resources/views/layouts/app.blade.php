<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Studio') — {{ $activeCompany->name ?? 'Dashboard' }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    :root {
      --color-primary: {{ $primaryColor ?? '#6366f1' }};
      --color-primary-dark: {{ $primaryColor ?? '#6366f1' }}dd;
      --color-secondary: {{ $secondaryColor ?? '#f59e0b' }};
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

@stack('scripts')
</body>
</html>
