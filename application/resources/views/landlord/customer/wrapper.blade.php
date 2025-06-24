@extends('landlord.layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- bread crumbs -->
        @include('landlord.misc.crumbs')
        <!-- bread crumbs -->

        <!-- action buttons -->
        @include('landlord.customer.actions.page-actions')
        <!-- action buttons -->

    </div>
    <!--page heading-->


    <!-- Row -->
    <div class="row">
        <!-- Column -->
        <div class="col-lg-4 col-xlg-3 col-md-5">
            <div class="card">
                <div class="card-body">
                    <center class="m-t-30">
                        <h3 class="card-title m-t-10 m-b-10">{{ $customer->tenant_name }}</h3>
                        <h5 class="card-subtitle"><a href="https://{{ $customer->domain }}"
                                target="_blank">{{ $customer->domain }}</a></h5>
                        <div class="row text-center justify-content-md-center">
                            <span
                                class="label label-lg {{ runtimeCustomerStatusColors($customer->tenant_status) }} words-uppercase-first"
                                id="customer_account_status">{{ runtimeCustomerStatusLang($customer->tenant_status) }}</span>
                        </div>
                    </center>
                </div>
                <div>
                    <hr class="m-t-15 m-b-15">
                </div>
                <div class="card-body p-t-0">
                    <small class="text-muted">@lang('lang.email')</small>
                    <h6>{{ $customer->tenant_email }}</h6>
                    <small class="text-muted p-t-30 db">@lang('lang.customer_signup_date')</small>
                    <h6>{{ runtimeDate($customer->tenant_created) }}</h6>
                    <div class="m-l--25 m-r--25">
                        <hr>
                    </div>
                    <small class="text-muted p-t-10 db">@lang('lang.package')</small>
                    <h6>{{ $customer->package_name ?? '---' }}</h6>
                    <small class="text-muted p-t-30 db">@lang('lang.package_type')</small>
                    <h6>
                        @if($customer->subscription_type == 'free')
                        @lang('lang.free_package')
                        @elseif($customer->subscription_type == 'paid')
                        @lang('lang.paid_package')
                        @else
                        ---
                        @endif
                    </h6>
                    <small class="text-muted p-t-30 db">@lang('lang.subscription_start_date')</small>
                    <h6>{{ runtimeDate($customer->subscription_created) }}</h6>
                    @if($customer->subscription_type == 'paid')
                    <small class="text-muted p-t-30 db">@lang('lang.free_trial')</small>
                    <h6>
                        @if($customer->subscription_status == 'free-trial')
                        <span class="uc-words">@lang('lang.yes')</span>
                        @else
                        <span class="uc-words">@lang('lang.no')</span>
                        @endif
                    </h6>
                    @if($customer->subscription_status == 'free-trial')
                    <small class="text-muted p-t-30 db">@lang('lang.trial_end_date')</small>
                    <h6>
                        <span class="uc-words">{{ runtimeDate($customer->subscription_trial_end) }}</span>
                    </h6>
                    @endif
                    @endif
                    <small class="text-muted p-t-30 db">@lang('lang.database_name')</small>
                    <h6>{{ $customer->database }}</h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <!-- Column -->
        <div class="col-lg-8 col-xlg-9 col-md-7">
            <div class="card">
                <!-- MENU -->
                <ul class="nav nav-tabs profile-tab" role="tablist">
                    <!--timeline-->
                    <li class="nav-item"> <a class="nav-link active cursor-pointer ajax-request" data-toggle="tab"
                            data-url="{{ url('app-admin/events?ref-source=customer&ref=customer&source=ext&action=load&event_customer_id='.$customer->tenant_id) }}"
                            data-loading-target="dynamic-content-container" role="tab">@lang('lang.timeline')</a>
                    </li>
                    <!--subscription-->
                    <li class="nav-item"> <a class="nav-link cursor-pointer ajax-request" data-toggle="tab"
                            data-url="{{ url('app-admin/customers/'.$customer->tenant_id.'/subscription?source=ext') }}"
                            data-loading-target="dynamic-content-container" role="tab">@lang('lang.subscription')</a>
                    </li>
                    <!--payments-->
                    <li class="nav-item dropdown {{ $page['tabmenu_more'] ?? '' }}">
                        <a class="nav-link dropdown-toggle  tabs-menu-item" data-loading-class="loading-tabs"
                            data-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true"
                            id="tabs-menu-billing" aria-expanded="false">
                            <span class="hidden-xs-down">{{ cleanLang(__('lang.payments')) }}</span>
                        </a>
                        <div class="dropdown-menu" x-placement="bottom-start" id="fx-topnav-dropdown">

                            <!--online-->
                            <a class="dropdown-item tabs-menu-item   js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_tickets'] ?? '' }}"
                                id="tabs-menu-tickets" data-toggle="tab" data-loading-class="loading-tabs"
                                data-loading-target="dynamic-content-container"
                                data-url="{{ url('app-admin/payments?source=ext&payment_tenant_id='.$customer->tenant_id) }}"
                                href="javascript:void(0);" role="tab">{{ cleanLang(__('lang.online')) }}</a>

                            <!--offline-->
                            <a class="dropdown-item tabs-menu-item   js-dynamic-url js-ajax-ux-request {{ $page['tabmenu_tickets'] ?? '' }}"
                                id="tabs-menu-tickets" data-toggle="tab" data-loading-class="loading-tabs"
                                data-loading-target="dynamic-content-container"
                                data-url="{{ url('app-admin/offline-payments?source=ext&proof_tenant_id='.$customer->tenant_id) }}"
                                href="javascript:void(0);" role="tab">{{ cleanLang(__('lang.offline')) }}</a>
                        </div>
                    </li>

                    <!--email settings-->
                    <li class="nav-item"> <a class="nav-link cursor-pointer ajax-request" data-toggle="tab"
                            data-url="{{ url('app-admin/customers/'.$customer->tenant_id.'/email?source=page') }}"
                            data-loading-target="dynamic-content-container" role="tab">@lang('lang.email_settings')</a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="home" role="tabpanel">
                        <div class="card-body dynamic-content-container">
                            <div id="dynamic-content-container">
                                <!--dynamic events-->
                            </div>
                            <!--load more button-->
                            @include('landlord.misc.load-more-dynamic-button')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>
    <!-- Row -->

</div>
<!--main content -->

<!--dynamically load timeline events-->
<script src="public/js/landlord/dynamic/timeline.js?v={{ config('system.versioning') }}"
    id="dynamic-load-timeline-events" data-loading-target="customer-content-container" data-progress-bar="hidden"
    data-url="{{ url('app-admin/events?ref-source=customer&source=customer&event_customer_id='.$customer->tenant_id) }}">
</script>
@endsection