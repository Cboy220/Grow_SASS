<div class="row p-t-30 top-stats-panels">
    <!-- Column -->
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-row">
                    <div class="round align-self-center round-success x-icon"><i class="sl-icon-target"></i></div>
                    <div class="m-l-10 align-self-center">
                        <h3 class="m-b-0">{{ runtimeMoneyFormat($stats['income_today']) }}</h3>
                        <h5 class="text-muted m-b-0">@lang('lang.today')</h5></div>
                </div>
            </div>
            
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-row">
                    <div class="round align-self-center round-info x-icon"><i class="sl-icon-chart"></i></div>
                    <div class="m-l-10 align-self-center">
                        <h3 class="m-b-0">{{ runtimeMoneyFormat($stats['income_this_month']) }}</h3>
                        <h5 class="text-muted m-b-0">@lang('lang.this_month')</h5></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-row">
                    <div class="round align-self-center round-primary x-icon"><i class="sl-icon-graph"></i></div>
                    <div class="m-l-10 align-self-center">
                        <h3 class="m-b-0">{{ runtimeMoneyFormat($stats['income_this_year']) }}</h3>
                        <h5 class="text-muted m-b-0">@lang('lang.this_year')</h5></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-row">
                    <div class="round align-self-center round-danger x-icon"><i class="sl-icon-people"></i></div>
                    <div class="m-l-10 align-self-center">
                        <h3 class="m-b-0">{{ $stats['count_customers'] }}</h3>
                        <h5 class="text-muted m-b-0">@lang('lang.customers')</h5></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
</div>