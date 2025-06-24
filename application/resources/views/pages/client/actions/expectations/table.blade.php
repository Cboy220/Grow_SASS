<table class="table table-hover mb-0">
    <thead>
        <tr>
            <th>{{ __('lang.goal') }}</th>
            <th>{{ __('lang.status') }}</th>
            <th>{{ __('lang.target_date') }}</th>
            <th>{{ __('lang.completion_percent') }}</th>
            <th>{{ __('lang.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expectations as $expectation)
            @include('pages.client.expectations.row', ['expectation' => $expectation])
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">{{ __('lang.no_expectations_set') }}</td>
            </tr>
        @endforelse
    </tbody>
</table> 