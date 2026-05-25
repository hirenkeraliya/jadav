@extends('layouts.app')
@section('title', 'Companies')
@section('breadcrumb', 'Admin / Companies')

@section('content')
<div class="page-header">
  <h1 class="page-title">Companies</h1>
  <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">+ New Company</a>
</div>

<div class="card">
  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Currency</th>
          <th>Users</th>
          <th>Projects</th>
          <th>Status</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($companies as $company)
        <tr>
          <td>
            <div style="font-weight:700">{{ $company->name }}</div>
            @if($company->primary_color)
            <div style="display:flex;gap:4px;margin-top:4px">
              <span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:{{ $company->primary_color }};border:1px solid rgba(0,0,0,0.08)"></span>
              <span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:{{ $company->secondary_color ?? '#f59e0b' }};border:1px solid rgba(0,0,0,0.08)"></span>
            </div>
            @endif
          </td>
          <td style="color:#6b7280">{{ $company->email }}</td>
          <td>{{ $company->currency_symbol ?? '' }} {{ $company->currency }}</td>
          <td>{{ $company->users->count() }}</td>
          <td>{{ $company->projects->count() }}</td>
          <td><span class="badge badge-{{ $company->is_active ? 'active' : 'inactive' }}">{{ $company->is_active ? 'Active' : 'Inactive' }}</span></td>
          <td>
            <div style="display:flex;gap:6px;justify-content:flex-end">
              <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-secondary btn-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> View
              </a>
              <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-secondary btn-xs">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit
              </a>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#9ca3af;padding:24px">No companies yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
