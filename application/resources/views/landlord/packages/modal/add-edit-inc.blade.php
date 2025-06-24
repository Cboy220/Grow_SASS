<div class="row">
    <div class="col-lg-12">


        <!--MAIN DETAILS-->
        <div class="modal-selector">


            <!--title-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">@lang('lang.package_name')</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="package_name" name="package_name"
                        value="{{ $package->package_name ?? '' }}">
                </div>
            </div>

            <!--billing cycles-->
            @if(config('visibility.payment_options') && config('visibility.package_type'))
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">@lang('lang.payment_options')</label>
                <div class="col-sm-12 col-lg-4">
                    <select class="select2-basic form-control form-control-sm select2-preselected"
                        id="package_subscription_options" name="package_subscription_options"
                        data-preselected="{{ $package->package_subscription_options ?? 'paid'}}">
                        <option value="paid">@lang('lang.paid')</option>
                        <option value="free">@lang('lang.free_package')</option>
                    </select>
                </div>
            </div>
            @endif

            <!--billing monthly price-->
            @if(config('visibility.payment_options'))
            <div
                class="option-billable {{ saasToggleSubscriptionOption($package->package_subscription_options ?? 'paid') }}">
                <div class="form-group row">
                    <label
                        class="col-sm-12 col-lg-3 text-left control-label col-form-label">@lang('lang.monthly_price')</label>
                    <div class="col-sm-12 col-lg-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"
                                id="package_amount_monthly_addon">{{ config('system.settings_system_currency_code') }}</span>
                            <input type="number" class="form-control" name="package_amount_monthly"
                                value="{{ $package->package_amount_monthly ?? ''}}"
                                aria-describedby="package_amount_monthly_addon">
                        </div>
                    </div>
                </div>


                <!--billing monthly price-->

                <div class="form-group row">
                    <label
                        class="col-sm-12 col-lg-3 text-left control-label col-form-label">@lang('lang.yearly_price')</label>
                    <div class="col-sm-12 col-lg-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"
                                id="package_amount_yearly_addon">{{ config('system.settings_system_currency_code') }}</span>
                            <input type="number" class="form-control" name="package_amount_yearly"
                                value="{{ $package->package_amount_yearly ?? '' }}"
                                aria-describedby="package_amount_yearly_addon">
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!--featured package-->
            <div class="form-group form-group-checkbox row">
                <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">@lang('lang.featured')</label>
                <div class="col-sm-12 col-lg-4" style="padding-top:5px;">
                    <input type="checkbox" id="package_featured" name="package_featured"
                        {{ runtimePrechecked($package->package_featured ?? '') }} class="filled-in chk-col-light-blue">
                    <label class="p-l-30" for="package_featured"></label>
                </div>
            </div>

            <!--change pricing warning-->
            @if(config('visibility.payment_gateways_editing_mode'))
            @if($package->package_subscription_options == 'paid')
            <div class="alert alert-info">@lang('lang.pricing_changes_to_package_info')</div>
            @else
            <div class="alert alert-info">@lang('lang.free_plan_cannot_change_pricing')</div>
            @endif
            @endif
        </div>

        <!--PACKAGE LIMITS-->
        <div class="form-group row">
            <div class="col-12">
                <div class="form-heading">
                    <span>@lang('lang.package_limits')</span>
                </div>
            </div>
        </div>

        <!--limit clients-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label">@lang('lang.maximum_clients')</label>
            <div class="col-sm-12 col-lg-4">
                <input type="number" class="form-control form-control-sm" id="package_limits_clients"
                    name="package_limits_clients" value="{{ $package->package_limits_clients ?? '' }}">
            </div>
        </div>

        <!--limits projects-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label">@lang('lang.maximum_projects')</label>
            <div class="col-sm-12 col-lg-4">
                <input type="number" class="form-control form-control-sm" id="package_limits_projects"
                    name="package_limits_projects" value="{{ $package->package_limits_projects ?? '' }}">
            </div>
        </div>

        <!--limits employees-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label">@lang('lang.maximum_employees')</label>
            <div class="col-sm-12 col-lg-4">
                <input type="number" class="form-control form-control-sm" id="package_limits_team"
                    name="package_limits_team" value="{{ $package->package_limits_team ?? '' }}">
            </div>
        </div>
        <div class="alert alert-info">@lang('lang.for_unlimited_use_minus_1')</div>

        <div class="line"></div>

        <!--PACKAGE MODULES-->
        <div class="form-group row">
            <div class="col-12">
                <div class="form-heading">
                    <span>@lang('lang.package_modules')</span>
                </div>
            </div>
        </div>


        <!--PACKAGE MODULES-->
        <div class="row">
            <!--tasks-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_tasks" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_tasks ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.tasks')</span>
                </label>
            </div>

            <!--invoices-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_invoices" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_invoices ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.invoices')</span>
                </label>
            </div>


            <!--leads-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_leads" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_leads ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.leads')</span>
                </label>
            </div>

            <!--knowledgebase-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_knowledgebase" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_knowledgebase ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.knowledgebase')</span>
                </label>
            </div>

            <!--estimates-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_estimates" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_estimates ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.estimates')</span>
                </label>
            </div>

            <!--expenses-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_expense" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_expense ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.expenses')</span>
                </label>
            </div>


            <!--package_module_proposals-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_proposals" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_proposals ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.proposals')</span>
                </label>
            </div>


            <!--package_module_contracts-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_contracts" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_contracts ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.contracts')</span>
                </label>
            </div>

            <!--package_module_messages-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_messages" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_messages ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.instant_messaging')</span>
                </label>
            </div>

            <!--subscriptions-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_subscriptions" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_subscriptions ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.subscriptions')</span>
                </label>
            </div>

            <!--tickets-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_tickets" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_tickets ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.tickets')</span>
                </label>
            </div>

            <!--calendar-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_calendar" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_calendar ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.calendar')</span>
                </label>
            </div>

            <!--time_tracking-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_timetracking" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_timetracking ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.time_tracking')</span>
                </label>
            </div>

            <!--reminders-->
            <div class="col-sm-12 col-lg-4 m-b-10">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="package_module_reminders" class="custom-control-input"
                        {{ runtimePrechecked($package->package_module_reminders ?? '') }}>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">@lang('lang.reminders')</span>
                </label>
            </div>
        </div>
    </div>
</div>