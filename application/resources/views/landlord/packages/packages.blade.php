<div class="col-sm-12 col-md-6 col-lg-4 col-xl-3" id="package_{{ $package->package_id }}">
    <div class="package-pricing-box m-b-40">
        <div class="pricing-header">
            <div class="x-heading">
                {{ $package->package_name }}
                @if($package->package_featured == 'yes')
                <span class="sl-icon-star text-danger p-l-5" data-toggle="tooltip"
                    title="@lang('lang.featured')"></span>
                @endif
            </div>
            <div class="x-price-month">
                {{ runtimeMoneyFormatSaaS($package->package_amount_monthly) }}<span>/@lang('lang.month')</span>
            </div>
            <div class="x-price-cycle">
                {{ runtimeMoneyFormatSaaS($package->package_amount_yearly) }}<span>/@lang('lang.year')</span>
            </div>
        </div>
        <div class="price-table-content">
            <div class="price-row"><strong>{{ $package->package_limits_clients }}</strong> @lang('lang.clients')</div>
            <div class="price-row"><strong>{{ $package->package_limits_projects }}</strong> @lang('lang.project')</div>
            <div class="price-row"><strong>{{ $package->package_limits_team }}</strong> @lang('lang.team_members')
            </div>

            <!--package_module_tasks-->
            <div class="price-row">
                @if($package->package_module_tasks == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.tasks')
            </div>

            <!--package_module_invoices-->
            <div class="price-row">
                @if($package->package_module_invoices == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.invoices')
            </div>

            <!--package_module_estimates-->
            <div class="price-row">
                @if($package->package_module_estimates == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.estimates')
            </div>

            <!--package_module_proposals-->
            <div class="price-row">
                @if($package->package_module_proposals == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.proposals')
            </div>

            <!--package_module_contracts-->
            <div class="price-row">
                @if($package->package_module_contracts == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.contracts')
            </div>

            <!--settings_modules_messages-->
            <div class="price-row">
                @if($package->package_module_messages == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.instant_messaging')
            </div>

            <!--package_module_expense-->
            <div class="price-row">
                @if($package->package_module_expense == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.expenses')
            </div>

            <!--package_module_leads-->
            <div class="price-row">
                @if($package->package_module_leads == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.leads')
            </div>

            <!--package_module_knowledgebase-->
            <div class="price-row">
                @if($package->package_module_knowledgebase == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.knowledgebase')
            </div>

            <!--package_module_subscriptions-->
            <div class="price-row">
                @if($package->package_module_subscriptions == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.subscriptions')
            </div>

            <!--package_module_tickets-->
            <div class="price-row">
                @if($package->package_module_tickets == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.tickets')
            </div>

            <!--package_module_calendar-->
            <div class="price-row">
                @if($package->package_module_calendar == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.calendar')
            </div>

            <!--package_module_timetracking-->
            <div class="price-row">
                @if($package->package_module_timetracking == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.time_tracking')
            </div>

            <!--package_module_reminders-->
            <div class="price-row">
                @if($package->package_module_reminders == 'yes')
                <i class="sl-icon-check text-success"></i>
                @else
                <i class="sl-icon-close text-danger"></i>
                @endif
                @lang('lang.reminders')
            </div>
        </div>

        <!--LANDLORD BUTTONS-->
        @if(config('visibility.landlord'))
        <div class="price-footer p-t-20 p-b-10">
            <!--archive-->
            @if($package->package_status == 'active')
            <button type="button" class="btn btn-warning btn-sm confirm-action-danger"
                data-confirm-title="@lang('lang.archive')" data-confirm-text="@lang('lang.are_you_sure')"
                data-ajax-type="GET" data-url="{{ url('app-admin/packages/'.$package->package_id).'/archive' }}">
                @lang('lang.archive')
            </button>
            @endif

            <!--restore-->
            @if($package->package_status == 'archived')
            <button type="button" class="btn btn-success btn-sm confirm-action-danger"
                data-confirm-title="@lang('lang.restore')" data-confirm-text="@lang('lang.are_you_sure')"
                data-ajax-type="GET" data-url="{{ url('app-admin/packages/'.$package->package_id).'/restore' }}">
                @lang('lang.restore')
            </button>
            @endif

            <!--delete-->
            @if($package->count_subscriptions > 0)
            <a href="javascript:void(0);" class="btn btn-default btn-sm" disabled="disabled" data-toggle="tooltip"
                title="@lang('lang.package_has_subscriptions_cannot_delete')">
                @lang('lang.delete')
            </a>
            @else
            <button type="button" class="btn btn-danger btn-sm confirm-action-danger"
                data-confirm-title="@lang('lang.delete')" data-confirm-text="@lang('lang.are_you_sure')"
                data-ajax-type="DELETE" data-url="{{ url('app-admin/packages/'.$package->package_id) }}">
                @lang('lang.delete')
            </button>
            @endif
            <!--edit-->
            <button type="button"
                class="btn btn-info btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ url('app-admin/packages/'.$package->package_id).'/edit' }}"
                data-loading-target="commonModalBody" data-modal-title="@lang('lang.edit')" data-modal-size="modal-xl"
                data-action-url="{{ url('app-admin/packages/'.$package->package_id)}}" data-action-method="PUT"
                data-action-ajax-class="js-ajax-ux-request">
                @lang('lang.edit')
            </button>

        </div>
        @endif

        <!--TENANT BUTTONS-->
        @if(config('visibility.tenant'))
        <div class="price-footer p-t-20 p-b-10">


            <!--this is your current plan-->
            @if(config('system.settings_saas_package_id') == $package->package_id)
            <span
                class="label label-light-inverse p-t-9 p-b-9 p-l-10 p-r-10">@lang('lang.this_is_your_current_plan')</span>
            @else
            <button type="button" class="btn btn-info btn-sm js-ajax-ux-request"
                data-url="{{ url('settings/account/'.$package->package_id).'/change-plan' }}">
                @lang('lang.select_plan')
            </button>
            @endif
        </div>
        @endif
    </div>
</div>