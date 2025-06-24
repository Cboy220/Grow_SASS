<ul class="list-group list-group-flush" id="expectationList">
    @forelse ($expectations as $ex)
        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 ps-0">

            {{-- Left: Status toggle + title --}}
            <div class="d-flex align-items-center flex-grow-1">
                <button style="font-size: 1.5rem" class="btn btn-sm p-0 mr-3 toggle-status"
                    data-action-url="{{ route('expectation.toggleCheck', $ex->client_expectation_id) }}"
                    data-id="{{ $ex->client_expectation_id }}" style="background: none; border: none;">
                    @if ($ex->status === 'fulfilled')
                        <i class="fas fa-check-circle text-info"></i>
                    @else
                        <i class="far fa-circle text-secondary"></i>
                    @endif
                </button>
                <span>{{ $ex->title }}</span>
            </div>

            {{-- Right: due_date + status + actions --}}
            <div class="d-flex align-items-center text-nowrap">
                <small class="text-muted mr-3">
                    <i class="far fa-calendar-alt mr-1"></i>
                    {{ \Carbon\Carbon::parse($ex->due_date)->format('Y-m-d') }}
                </small>

                <span class="badge badge-{{ $ex->status === 'fulfilled' ? 'success' : 'warning' }}">
                    {{ $ex->status }}
                </span>

                <button
                    class="btn btn-sm btn-outline-secondary ml-3 edit-btn edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    data-id="{{ $ex->client_expectation_id }}" data-toggle="modal" data-target="#basicModal"
                    data-url="{{ url('/expectation/edit/' . $ex->client_expectation_id) }}"
                    data-loading-target="basicModal">
                    <i class="fas fa-edit"></i>
                </button>

                <button
                    class="btn btn-sm btn-outline-danger ml-2 delete-btn edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    data-id="{{ $ex->client_expectation_id }}" data-toggle="modal" data-target="#basicModal"
                    data-url="{{ url('/expectation/delete/' . $ex->client_expectation_id) }}"
                    data-loading-target="basicModal">
                    <i class="fas fa-trash-alt"></i>
                </button>
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
