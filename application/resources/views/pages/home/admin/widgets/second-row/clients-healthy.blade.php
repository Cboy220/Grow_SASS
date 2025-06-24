@php
    $stats = $payload['stats'];
@endphp

<div class="col-lg-3 col-md-12" id="dashboard-widgets-client-healthy-status">
    <div class="card">
        <div class="card-body">
            <div class="d-flex m-b-30 no-block">
                <h5 class="card-title m-b-0 align-self-center">{{ __('lang.customer_success') }}</h5>
            </div>

            <div class="card customer-success-block mb-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <span>{{ __('lang.expectation_compliance_percent') }}</span>
                    </div>
                    <div class="font-weight-bold text-success">{{ $stats['expectation_percent'] }}%</div>
                </div>
            </div>

            <div class="card customer-success-block mb-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-star text-warning mr-2"></i>
                        <span>{{ __('lang.average_feedback') }}</span>
                    </div>
                    <div class="font-weight-bold text-primary">{{ $stats['average_feedback'] }}</div>
                </div>
            </div>

            <div class="card customer-success-block mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-comment-dots text-info mr-2"></i>
                        <span>Últimos comentarios</span>
                    </div>
                    <div class="pl-4">
                        @forelse ($stats['recent_comments'] as $comment)
                            <div>“{{ $comment }}”</div>
                        @empty
                            <div class="text-muted">No hay comentarios recientes.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card customer-success-block mb-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <i
                            class="fas fa-heart 
                    @if ($stats['health_status'] === 'green') text-success
                    @elseif($stats['health_status'] === 'yellow') text-warning
                    @else text-danger @endif
                    mr-2"></i>
                        <span>Salud del cliente</span>
                    </div>
                    <div
                        class="font-weight-bold
                    @if ($stats['health_status'] === 'green') text-success
                    @elseif($stats['health_status'] === 'yellow') text-warning
                    @else text-danger @endif">
                        {{ ucfirst($stats['health_status']) }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
