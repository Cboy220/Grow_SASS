<!DOCTYPE html>
<html lang="en">

@include('frontend.layout.header')

<body class="inner-page pricing">

    @include('frontend.layout.menu')

    @include('frontend.layout.preloader')


    <!--page heading-->
    <div class="container page-wrapper pricing">

        <div class="page-header pricing-header text-center">
            <h2>{!! _clean($content->frontend_data_1) !!}</h2>
            <h5>{!! _clean($content->frontend_data_2) !!}</h5>
        </div>


        <!--pricing toggle-->
        <div class="switch-container">
            <!-- Rounded switch -->
            <h4 class="pricing-toggle-period active" id="pricing-toggle-monthly">@lang('lang.monthly')</h4>

            <label class="switch">
                <input id="price-cycle-switch" type="checkbox">
                <span class="slider round"></span>
            </label>

            <h4 class="pricing-toggle-period" id="pricing-toggle-yearly">@lang('lang.yearly')</h4>
        </div>



        <!--[MONTHLY]-->
        <div class="pricing-table-wrapper" id="pricing-tables-monthly">
            <div class="pricing-table card-deck row" id="pricing-tables-monthly">

                <!--each plan-->
                @foreach($packages as $package)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="card box-shadow pricing-plan pricing-featured-{{ $package->package_featured }}">
                        <div class="card-body">
                            <h4 class="my-0 font-weight-normal">{{ $package->package_name }}</h4>
                            <h1 class="card-title pricing-card-title">
                                {{ runtimeMoneyFormatPricing($package->package_amount_monthly) }} <small
                                    class="text-muted">/
                                    @lang('lang.month')</small></h1>
                            <ul class="list-unstyled mt-3 mb-4">


                                <!--package_limits_team-->
                                <li>
                                    @if($package->package_limits_team > 0 || $package->package_limits_team == -1)
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text font-weight-500">@lang('lang.team') (@lang('lang.users')) -
                                        {{ runtimeCheckUnlimited($package->package_limits_team) }}</span>
                                </li>

                                <!--package_limits_clients-->
                                <li>
                                    @if($package->package_limits_clients > 0 || $package->package_limits_clients == -1)
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text font-weight-500">@lang('lang.clients') -
                                        {{ runtimeCheckUnlimited($package->package_limits_clients) }}</span>
                                </li>


                                <!--package_limits_projects-->
                                <li>
                                    @if($package->package_limits_projects > 0 || $package->package_limits_projects ==
                                    -1)
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text font-weight-500">@lang('lang.projects') -
                                        {{ runtimeCheckUnlimited($package->package_limits_projects) }}</span>
                                </li>

                                <!--package_module_tasks-->
                                <li>
                                    @if($package->package_module_tasks == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.tasks')</span>
                                </li>

                                <!--package_module_leads-->
                                <li>
                                    @if($package->package_module_leads == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.leads')</span>
                                </li>

                                <!--package_module_invoices-->
                                <li>
                                    @if($package->package_module_invoices == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.invoices')</span>
                                </li>

                                <!--package_module_estimates-->
                                <li>
                                    @if($package->package_module_estimates == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.estimates')</span>
                                </li>

                                <!--package_module_subscriptions-->
                                <li>
                                    @if($package->package_module_subscriptions == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.subscriptions')</span>
                                </li>

                                <!--package_module_contracts-->
                                <li>
                                    @if($package->package_module_contracts == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.contracts')</span>
                                </li>

                                <!--package_module_proposals-->
                                <li>
                                    @if($package->package_module_proposals == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.proposals')</span>
                                </li>

                                <!--package_module_tickets-->
                                <li>
                                    @if($package->package_module_tickets == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.tickets')</span>
                                </li>

                                <!--package_module_calendar-->
                                <li>
                                    @if($package->package_module_calendar == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.calendar')</span>
                                </li>

                                <!--package_module_expense-->
                                <li>
                                    @if($package->package_module_expense == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.expenses')</span>
                                </li>

                                <!--package_module_messages-->
                                <li>
                                    @if($package->package_module_messages == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.instant_messaging')</span>
                                </li>

                                <!--package_module_timetracking-->
                                <li>
                                    @if($package->package_module_timetracking == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.time_tracking')</span>
                                </li>

                                <!--package_module_knowledgebase-->
                                <li>
                                    @if($package->package_module_knowledgebase == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.knowledgebase')</span>
                                </li>

                                <!--package_module_reminders-->
                                <li>
                                    @if($package->package_module_reminders == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.reminders')</span>
                                </li>
                            </ul>
                            @if($package->package_subscription_options == 'free')
                            <a type="button" href="{{ url('account/signup?ref=free_'.$package->package_id) }}"
                                class="frontent-pricing-button">{{ $content->frontend_data_4 ?? '' }}</a>
                            @else
                            <a type="button" href="{{ url('account/signup?ref=monthly_'.$package->package_id) }}"
                                class="frontent-pricing-button">{{ $content->frontend_data_4 ?? '' }}</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>


        <!--[YEARLY]-->
        <div class="pricing-table-wrapper hidden" id="pricing-tables-yearly">
            <div class="pricing-table card-deck row">

                <!--each plan-->
                @foreach($packages as $package)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="card box-shadow pricing-plan pricing-featured-{{ $package->package_featured }}">
                        <div class="card-body">
                            <h4 class="my-0 font-weight-normal">{{ $package->package_name }}</h4>
                            <h1 class="card-title pricing-card-title">
                                {{ runtimeMoneyFormatPricing($package->package_amount_yearly) }} <small
                                    class="text-muted">/
                                    @lang('lang.year')</small></h1>
                            <ul class="list-unstyled mt-3 mb-4">



                                <!--package_limits_team-->
                                <li>
                                    @if($package->package_limits_team > 0 || $package->package_limits_team == -1)
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text font-weight-500">@lang('lang.team') (@lang('lang.users')) -
                                        {{ runtimeCheckUnlimited($package->package_limits_team) }}</span>
                                </li>

                                <!--package_limits_clients-->
                                <li>
                                    @if($package->package_limits_clients > 0 || $package->package_limits_clients == -1)
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text font-weight-500">@lang('lang.clients') -
                                        {{ runtimeCheckUnlimited($package->package_limits_clients) }}</span>
                                </li>


                                <!--package_limits_projects-->
                                <li>
                                    @if($package->package_limits_projects > 0 || $package->package_limits_projects ==
                                    -1)
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text font-weight-500">@lang('lang.projects') -
                                        {{ runtimeCheckUnlimited($package->package_limits_projects) }}</span>
                                </li>

                                <!--package_module_tasks-->
                                <li>
                                    @if($package->package_module_tasks == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.tasks')</span>
                                </li>

                                <!--package_module_leads-->
                                <li>
                                    @if($package->package_module_leads == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.leads')</span>
                                </li>

                                <!--package_module_invoices-->
                                <li>
                                    @if($package->package_module_invoices == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.invoices')</span>
                                </li>

                                <!--package_module_estimates-->
                                <li>
                                    @if($package->package_module_estimates == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.estimates')</span>
                                </li>

                                <!--package_module_subscriptions-->
                                <li>
                                    @if($package->package_module_subscriptions == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.subscriptions')</span>
                                </li>

                                <!--package_module_contracts-->
                                <li>
                                    @if($package->package_module_contracts == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.contracts')</span>
                                </li>

                                <!--package_module_proposals-->
                                <li>
                                    @if($package->package_module_proposals == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.proposals')</span>
                                </li>

                                <!--package_module_tickets-->
                                <li>
                                    @if($package->package_module_tickets == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.tickets')</span>
                                </li>

                                <!--package_module_calendar-->
                                <li>
                                    @if($package->package_module_calendar == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.calendar')</span>
                                </li>

                                <!--package_module_expense-->
                                <li>
                                    @if($package->package_module_expense == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.expenses')</span>
                                </li>

                                <!--package_module_messages-->
                                <li>
                                    @if($package->package_module_messages == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.instant_messaging')</span>
                                </li>

                                <!--package_module_timetracking-->
                                <li>
                                    @if($package->package_module_timetracking == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.time_tracking')</span>
                                </li>

                                <!--package_module_knowledgebase-->
                                <li>
                                    @if($package->package_module_knowledgebase == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.knowledgebase')</span>
                                </li>

                                <!--package_module_reminders-->
                                <li>
                                    @if($package->package_module_reminders == 'yes')
                                    <span class="x-icon x-icon-yes"><i class="mdi mdi-check"></i></span>
                                    @else
                                    <span class="x-icon x-icon-no"><i class="mdi mdi-window-close"></i></span>
                                    @endif
                                    <span class="x-text">@lang('lang.reminders')</span>
                                </li>
                            </ul>
                            @if($package->package_subscription_options == 'free')
                            <a type="button" href="{{ url('account/signup?ref=free_'.$package->package_id) }}"
                                class="frontent-pricing-button">{{ $content->frontend_data_4 ?? '' }}</a>
                            @else
                            <a type="button" href="{{ url('account/signup?ref=yearly_'.$package->package_id) }}"
                                class="frontent-pricing-button">{{ $content->frontend_data_4 ?? '' }}</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach



            </div>
        </div>

        <!--FOOTER-->
        <div class="pricing-summary-content p-t-10">
            {!! $content->frontend_data_3 !!}
        </div>

    </div>

    @include('frontend.layout.footer')

    @include('frontend.layout.footerjs')
</body>

</html>