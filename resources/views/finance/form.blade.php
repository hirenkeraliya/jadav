@extends('layouts.app')
@section('title', 'Add Finance Entry')
@section('breadcrumb')
  <a href="{{ route('projects.show', $project) }}" style="color:#8b5cf6;text-decoration:none">{{ $project->name }}</a> /
  Finance / Add
@endsection

@section('content')
<div style="max-width:680px">
  <h1 class="page-title" style="margin-bottom:24px">{{ isset($entry) ? 'Edit Finance Entry' : 'Add Finance Entry' }}</h1>
  <div class="card">
    <div class="card-body">
      <form method="POST"
            action="{{ isset($entry) ? route('finance.update', [$project, $entry]) : route('finance.store', $project) }}"
            enctype="multipart/form-data">
        @csrf
        @if(isset($entry)) @method('PUT') @endif

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Type <span style="color:#ef4444">*</span></label>
            <select name="type" class="form-control" required>
              <option value="credit" {{ old('type', $entry->type ?? '') == 'credit' ? 'selected' : '' }}>Credit (Income)</option>
              <option value="debit" {{ old('type', $entry->type ?? '') == 'debit' ? 'selected' : '' }}>Debit (Expense)</option>
            </select>
          </div>
          <div>
            <label class="form-label">Category</label>
            <select name="finance_entry_type_id" class="form-control">
              <option value="">— Select Category —</option>
              @foreach($entryTypes as $et)
                <option value="{{ $et->id }}" {{ old('finance_entry_type_id', $entry->finance_entry_type_id ?? '') == $et->id ? 'selected' : '' }}>
                  {{ $et->name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Amount <span style="color:#ef4444">*</span></label>
            <input type="number" name="amount" step="0.01" min="0" class="form-control"
                   value="{{ old('amount', $entry->amount ?? '') }}" required>
          </div>
          <div>
            <label class="form-label">Date <span style="color:#ef4444">*</span></label>
            <input type="date" name="date" class="form-control"
                   value="{{ old('date', isset($entry) ? $entry->date->format('Y-m-d') : today()->format('Y-m-d')) }}" required>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label class="form-label">Payment Method</label>
            <select name="payment_type_id" class="form-control">
              <option value="">— Select —</option>
              @foreach($paymentTypes as $pt)
                <option value="{{ $pt->id }}" {{ old('payment_type_id', $entry->payment_type_id ?? '') == $pt->id ? 'selected' : '' }}>
                  {{ $pt->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="form-label">Reference Number</label>
            <input type="text" name="reference_number" class="form-control"
                   value="{{ old('reference_number', $entry->reference_number ?? '') }}" placeholder="Cheque/TXN no.">
          </div>
        </div>

        <div style="margin-bottom:16px">
          <label class="form-label">Remarks</label>
          <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $entry->remarks ?? '') }}</textarea>
        </div>

        @unless(isset($entry))
        <div style="margin-bottom:24px">
          <label class="form-label">Attach Files</label>
          <input type="file" name="files[]" multiple class="form-control">
        </div>
        @endunless

        <div style="display:flex;gap:10px">
          <button type="submit" class="btn btn-primary">{{ isset($entry) ? 'Update' : 'Save Entry' }}</button>
          <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
