<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password</title>
  @vite(['resources/css/app.css'])
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f8f7ff;font-family:'Inter',sans-serif;padding:20px">
  <div style="width:100%;max-width:420px">
    <div style="text-align:center;margin-bottom:28px">
      <h1 style="font-size:1.5rem;font-weight:800;color:#1e1b4b">Change Password</h1>
      <p style="color:#8b5cf6;font-size:0.9rem;margin-top:4px">Keep your account secure</p>
    </div>

    <div class="card">
      <div class="card-body">
        @include('partials.flash')
        <form method="POST" action="{{ route('password.change.post') }}">
          @csrf
          <div style="margin-bottom:16px">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
          </div>
          <div style="margin-bottom:16px">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div style="margin-bottom:24px">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%">Update Password</button>
        </form>
      </div>
    </div>

    <div style="text-align:center;margin-top:16px">
      <a href="{{ route('dashboard') }}" style="font-size:0.85rem;color:#8b5cf6">← Back to Dashboard</a>
    </div>
  </div>
</body>
</html>
