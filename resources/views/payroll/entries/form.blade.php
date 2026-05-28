@extends('layouts.app')
@section('title', isset($payrollEntry) ? 'Edit Time Entry' : 'Add Time Entry')
@section('breadcrumb')
  <a href="{{ route('payroll.entries.index') }}" style="color:#8b5cf6;text-decoration:none">Time Entries</a> /
  {{ isset($payrollEntry) ? 'Edit' : 'New' }}
@endsection

@section('content')
<div style="max-width:600px">
  <h1 class="page-title" style="margin-bottom:24px">
    {{ isset($payrollEntry) ? 'Edit Time Entry' : 'Add Time Entry' }}
  </h1>

  <div class="card">
    <div class="card-body">
      <form method="POST"
            action="{{ isset($payrollEntry) ? route('payroll.entries.update', $payrollEntry) : route('payroll.entries.store') }}">
        @csrf
        @if(isset($payrollEntry)) @method('PUT') @endif

        <div style="margin-bottom:16px">
          <label class="form-label">Staff Member <span style="color:#ef4444">*</span></label>
          <select name="staff_id" class="form-control {{ $errors->has('staff_id') ? 'error' : '' }}" required>
            <option value="">— Select staff —</option>
            @foreach($staffList as $member)
              <option value="{{ $member->id }}"
                {{ old('staff_id', $payrollEntry->staff_id ?? $selectedStaff) == $member->id ? 'selected' : '' }}>
                {{ $member->name }} @if($member->designation)({{ $member->designation }})@endif
              </option>
            @endforeach
          </select>
          @error('staff_id') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom:16px">
          <label class="form-label">Date <span style="color:#ef4444">*</span></label>
          <input type="date" name="entry_date" class="form-control {{ $errors->has('entry_date') ? 'error' : '' }}"
                 value="{{ old('entry_date', isset($payrollEntry) ? $payrollEntry->entry_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
          @error('entry_date') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Start Time <span style="color:#ef4444">*</span></label>
            <input type="time" name="start_time" class="form-control {{ $errors->has('start_time') ? 'error' : '' }}"
                   value="{{ old('start_time', isset($payrollEntry) ? substr($payrollEntry->start_time, 0, 5) : '') }}" required>
            @error('start_time') <span class="form-error">{{ $message }}</span> @enderror
          </div>
          <div>
            <label class="form-label">End Time <span style="color:#ef4444">*</span></label>
            <input type="time" name="end_time" class="form-control {{ $errors->has('end_time') ? 'error' : '' }}"
                   value="{{ old('end_time', isset($payrollEntry) ? substr($payrollEntry->end_time, 0, 5) : '') }}" required>
            @error('end_time') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        {{-- Live hours preview --}}
        <div id="hours-preview" style="display:none;background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:0.9rem;color:#0369a1">
          Duration: <strong id="hours-value">0.00</strong> hrs
        </div>

        <div style="margin-bottom:24px">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="2"
                    placeholder="Optional note for this entry">{{ old('notes', $payrollEntry->notes ?? '') }}</textarea>
        </div>

        <div style="display:flex;gap:10px">
          <button type="submit" class="btn btn-primary">
            {{ isset($payrollEntry) ? 'Update Entry' : 'Save Entry' }}
          </button>
          <a href="{{ route('payroll.entries.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  (function () {
    const startInput = document.querySelector('[name="start_time"]');
    const endInput   = document.querySelector('[name="end_time"]');
    const preview    = document.getElementById('hours-preview');
    const display    = document.getElementById('hours-value');

    function updatePreview() {
      const s = startInput.value;
      const e = endInput.value;
      if (!s || !e) { preview.style.display = 'none'; return; }
      const [sh, sm] = s.split(':').map(Number);
      const [eh, em] = e.split(':').map(Number);
      const diff = (eh * 60 + em) - (sh * 60 + sm);
      if (diff <= 0) { preview.style.display = 'none'; return; }
      display.textContent = (diff / 60).toFixed(2);
      preview.style.display = 'block';
    }

    startInput.addEventListener('change', updatePreview);
    endInput.addEventListener('change', updatePreview);
    updatePreview();
  })();
</script>
@endpush
@endsection
