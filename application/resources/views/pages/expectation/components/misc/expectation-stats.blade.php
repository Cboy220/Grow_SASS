<div class="mb-2 text-muted small">
  <i class="fas fa-check-circle text-success mr-1"></i> {{ $stats['fulfilled'] }} {{ __('lang.fulfilled') }} &nbsp;&nbsp;
  <i class="fas fa-hourglass-half text-warning mr-1"></i> {{ $stats['pending'] }} {{ __('lang.pending') }} &nbsp;&nbsp;
  <i class="fas fa-clock text-danger mr-1"></i> {{ $stats['overdue'] }} {{ __('lang.overdue') }}
</div>

<div class="progress mb-4 text-white text-center" style="height: 1.5rem; font-size: 0.9rem; font-weight: 500;">
  @if ($stats['fulfilledPercent'] > 0)
      <div class="progress-bar bg-success d-flex align-items-center justify-content-center"
          style="width: {{ $stats['fulfilledPercent'] }}%">
          <span class="d-flex align-items-center">
              <i class="fas fa-check-circle"></i><span class="ml-1">{{ $stats['fulfilledPercent'] }}%</span>
          </span>
      </div>
  @endif

  @if ($stats['pendingPercent'] > 0)
      <div class="progress-bar bg-warning text-dark d-flex align-items-center justify-content-center"
          style="width: {{ $stats['pendingPercent'] }}%">
          <span class="d-flex align-items-center">
              <i class="fas fa-hourglass-half"></i><span class="ml-1">{{ $stats['pendingPercent'] }}%</span>
          </span>
      </div>
  @endif

  @if ($stats['overduePercent'] > 0)
      <div class="progress-bar bg-danger d-flex align-items-center justify-content-center"
          style="width: {{ $stats['overduePercent'] }}%">
          <span class="d-flex align-items-center">
              <i class="fas fa-clock"></i><span class="ml-1">{{ $stats['overduePercent'] }}%</span>
          </span>
      </div>
  @endif
</div>
