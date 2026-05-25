@if(session('success'))
<div class="alert alert-success mb-4" role="alert">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><polyline points="20 6 9 17 4 12"/></svg>
  <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="alert alert-error mb-4" role="alert">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
  <span>{{ session('error') }}</span>
</div>
@endif

@if($errors->any())
<div class="alert alert-error mb-4" role="alert">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  <div>
    @foreach($errors->all() as $err)
      <div>{{ $err }}</div>
    @endforeach
  </div>
</div>
@endif
