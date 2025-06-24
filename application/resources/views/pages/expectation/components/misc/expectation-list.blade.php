<ul class="list-group list-group-flush">
  @forelse ($expectations as $ex)
      <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 ps-0">
          {{-- Left: Check icon + title --}}
          <div class="d-flex align-items-center flex-grow-1">
              @if ($ex->status === 'fulfilled')
                  <i class="fas fa-check-circle text-info mr-2" style="font-size: 1.4rem;"></i>
              @else
                  <i class="far fa-circle text-secondary mr-2" style="font-size: 1.4rem;"></i>
              @endif
              <span>{{ $ex->title }}</span>
          </div>

          {{-- Right: due_date + status --}}
          <div class="d-flex align-items-center text-nowrap">
              @if ($ex->due_date)
                  <small class="text-muted mr-3">
                      <i class="far fa-calendar-alt mr-1"></i>
                      {{ \Carbon\Carbon::parse($ex->due_date)->format('Y-m-d') }}
                  </small>
              @endif

              <span class="badge badge-{{ $ex->status === 'fulfilled' ? 'success' : 'warning' }}">
                  {{ $ex->status }}
              </span>
          </div>
      </li>
  @empty
      <li class="list-group-item text-muted">{{ __('lang.no_expectations_found') }}</li>
  @endforelse
</ul>

{{-- Pagination --}}
<div class="mt-4" id="paginationLinks">
  {!! $expectations->links() !!}
</div>
