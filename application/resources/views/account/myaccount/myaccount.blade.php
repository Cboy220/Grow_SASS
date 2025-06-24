@extends('account.wrapper')
@section('account-page')
<div class="account-wrapper">


    <div class="row x-current-plan p-t-10">
        <div class="col-sm-12 col-lg-6">
            <h3>{{ $package->package_name }}</h3>
            @lang('lang.this_is_your_current_plan')
        </div>
        <div class="col-sm-12 col-lg-6 text-right">
            <button type="button"
                class="btn waves-effect waves-light btn-sm btn-success hidden">@lang('lang.change_your_plan')</button>

            <!--pay button-->
            @if($subscription->subscription_status == 'awaiting-payment')
            <button type="button"
                class="btn waves-effect waves-light btn-sm btn-danger js-ajax-ux-request js-submenu-ajax js-dynamic-settings-url"
                data-url="/settings/account/notices">@lang('lang.pay_now')</button>
            @endif
        </div>
    </div>
    <div class="x-current-features p-t-40">

        <div class="table-responsive">
            <table class="table">
                <tbody>
                    @if($subscription->subscription_type == 'paid' && $subscription->subscription_gateway_billing_cycle
                    == 'monthly')
                    <tr>
                        <td>@lang('lang.amount')</td>
                        <td class="text-right p-r-30">
                            {{ runtimeMoneyFormat($package->package_amount_monthly) }}/@lang('lang.month')
                        </td>
                    </tr>
                    @endif
                    @if($subscription->subscription_type == 'paid' && $subscription->subscription_gateway_billing_cycle
                    == 'yearly')
                    <tr>
                        <td>@lang('lang.amount')</td>
                        <td class="text-right p-r-30">
                            {{ runtimeMoneyFormat($package->package_amount_yearly) }}/@lang('lang.year')
                        </td>
                    </tr>
                    @endif
                    <!--status-->
                    <tr>
                        <td>@lang('lang.account_status')</td>
                        <td class="text-right p-r-30 font-medium"><span
                                class="label {{ runtimeSubscriptionStatusColors($subscription->subscription_status) }}">{{ runtimeSubscriptionStatusLang($subscription->subscription_status) }}</span>
                        </td>
                    </tr>

                    <!--trial_end_date-->
                    @if($subscription->subscription_status =='free-trial')
                    <tr>
                        <td>@lang('lang.trial_end_date')</td>
                        <td class="text-right p-r-30">
                            {{ runtimeDate($subscription->subscription_trial_end) }}
                        </td>
                    </tr>
                    @endif

                    <!--next_billing_date-->
                    <tr>
                        <td>@lang('lang.next_billing_date')</td>
                        <td class="text-right p-r-30">
                            @if($subscription->subscription_type == 'free')
                            <span>@lang('lang.none')</span>
                            @else
                            <span>{{ runtimeDate($subscription->subscription_date_next_renewal) }}</span>
                            @endif
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>


    <h4 class="m-b-0 p-b-15 m-t-20">@lang('lang.features')</h4>

    <div class="x-current-features">

        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr>
                        <td>@lang('lang.clients')</td>
                        <td class="text-right p-r-30">{{ runtimeCheckUnlimited($package->package_limits_clients) }}</td>
                    </tr>
                    <tr>
                        <td>@lang('lang.projects')</td>
                        <td class="text-right p-r-30">{{ runtimeCheckUnlimited($package->package_limits_projects) }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('lang.team_members')</td>
                        <td class="text-right p-r-30">
                            {{ runtimeCheckUnlimited($package->package_limits_team) }}
                        </td>
                    </tr>
                    <!--package_module_tasks-->
                    <tr>
                        <td>@lang('lang.tasks')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_tasks == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>

                    <!--package_module_invoices-->
                    <tr>
                        <td>@lang('lang.invoices')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_invoices == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>

                    <!--package_module_estimates-->
                    <tr>
                        <td>@lang('lang.estimates')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_estimates == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>

                    <!--package_module_proposals-->
                    <tr>
                        <td>@lang('lang.proposals')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_proposals == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>

                    <!--package_module_contracts-->
                    <tr>
                        <td>@lang('lang.contracts')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_contracts == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>

                    <!--package_module_messages-->
                    <tr>
                        <td>@lang('lang.instant_messaging')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_messages == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>

                    <!--package_module_expense-->
                    <tr>
                        <td>@lang('lang.expenses')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_expense == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>

                    <!--package_module_leads-->
                    <tr>
                        <td>@lang('lang.leads')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_leads == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>

                    <!--package_module_knowledgebase-->
                    <tr>
                        <td>@lang('lang.knowledgebase')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_knowledgebase == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>

                    <!--package_module_subscriptions-->
                    <tr>
                        <td>@lang('lang.subscriptions')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_subscriptions == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>

                    <!--package_module_tickets-->
                    <tr>
                        <td>@lang('lang.tickets')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_tickets == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>


                    <!--package_module_calendar-->
                    <tr>
                        <td>@lang('lang.calendar')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_calendar == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>

                    <!--package_module_timetracking-->
                    <tr>
                        <td>@lang('lang.time_tracking')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_timetracking == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>

                    <!--package_module_reminders-->
                    <tr>
                        <td>@lang('lang.reminders')</td>
                        <td class="text-right p-r-30">
                            @if($package->package_module_reminders == 'yes')
                            <i class="sl-icon-check text-success"></i>
                            @else
                            <i class="sl-icon-close text-danger"></i>
                            @endif</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>


    <!--cancel subscription-->
    <div class="modal-selector m-r-0 m-l-0 m-t-20 p-t-20 p-b-20">

        <div class="hidden" id="cancel_account_last_step">
            <div class="alert alert-danger">
                <h5 class="text-danger"><i class="mdi mdi-alert-outline"></i> @lang('lang.warning')</h5>
                @lang('lang.cancel_subscription_info')
            </div>

            <!--add item modal-->
            <div class="text-right">
                <button type="button" class="btn btn-danger btn-sm ajax-request"
                    data-url="{{ url('/settings/account/close-account') }}"
                    id="cancel_my_subscription_button_confirm">@lang('lang.cancel_account_confirmation')
                </button>
            </div>
        </div>

        <!--add item modal-->
        <div class="text-right" id="cancel_my_subscription_button_container">
            <button type="button" class="btn btn-info btn-sm"
                id="cancel_my_subscription_button">@lang('lang.close_my_account')
            </button>
        </div>

    </div>

</div>
@endsection